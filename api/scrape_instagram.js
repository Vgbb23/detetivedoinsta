export default async function handler(req, res) {
  // Enable CORS
  res.setHeader('Access-Control-Allow-Credentials', true);
  res.setHeader('Access-Control-Allow-Origin', '*');
  res.setHeader('Access-Control-Allow-Methods', 'GET,OPTIONS,PATCH,DELETE,POST,PUT');
  res.setHeader('Access-Control-Allow-Headers', 'X-CSRF-Token, X-Requested-With, Accept, Accept-Version, Content-Length, Content-MD5, Content-Type, Date, X-Api-Version');

  if (req.method === 'OPTIONS') {
    return res.status(200).end();
  }

  if (req.method !== 'POST') {
    return res.status(405).json({ ok: false, error: 'method_not_allowed' });
  }

  // Parse body from form-urlencoded
  let username = '';
  if (typeof req.body === 'string') {
    const params = new URLSearchParams(req.body);
    username = params.get('username') || '';
  } else if (req.body && req.body.username) {
    username = req.body.username;
  } else if (req.query && req.query.username) {
    username = req.query.username;
  }

  username = (username || '').trim().replace(/^@+/, '');
  
  if (!username || !/^[A-Za-z0-9._]+$/.test(username)) {
    return res.status(400).json({ ok: false, error: 'invalid_username' });
  }

  try {
    const profile = await fetchProfileData(username);
    
    if (profile && profile.profile_pic_url) {
      return res.status(200).json({ ok: true, data: profile });
    } else {
      return res.status(404).json({ ok: false, error: 'not_found' });
    }
  } catch (error) {
    console.error('Error fetching profile:', error);
    return res.status(500).json({ ok: false, error: 'server_error', message: error.message });
  }
}

async function fetchProfileData(username) {
  const url = `https://i.instagram.com/api/v1/users/web_profile_info/?username=${encodeURIComponent(username)}`;
  
  try {
    const response = await fetch(url, {
      headers: {
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'X-IG-App-ID': '936619743392459'
      }
    });

    if (!response.ok || response.status >= 400) {
      throw new Error('API request failed');
    }

    const json = await response.json();
    
    if (json && json.data && json.data.user) {
      const u = json.data.user;
      const pic = u.profile_pic_url_hd || u.profile_pic_url || null;
      const followers = u.edge_followed_by?.count ? parseInt(u.edge_followed_by.count, 10) : null;
      const following = u.edge_follow?.count ? parseInt(u.edge_follow.count, 10) : null;
      const posts = u.edge_owner_to_timeline_media?.count ? parseInt(u.edge_owner_to_timeline_media.count, 10) : null;
      const bio = u.biography || '';

      return {
        username: u.username || username,
        full_name: u.full_name || '',
        id: u.id || '',
        profile_pic_url: pic,
        biography: bio,
        followers: followers,
        following: following,
        posts: posts
      };
    }
  } catch (error) {
    // Fallback to HTML scraping
    try {
      const htmlUrl = `https://www.instagram.com/${encodeURIComponent(username)}/`;
      const htmlResponse = await fetch(htmlUrl, {
        headers: {
          'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        }
      });

      if (htmlResponse.ok) {
        const html = await htmlResponse.text();
        
        // Try multiple patterns for profile picture
        let picMatch = html.match(/property="og:image"\s+content="([^"]+)"/i);
        if (!picMatch) {
          picMatch = html.match(/"profile_pic_url_hd"\s*:\s*"([^"]+)"/i);
        }
        if (!picMatch) {
          picMatch = html.match(/"profile_pic_url"\s*:\s*"([^"]+)"/i);
        }
        
        let picUrl = null;
        if (picMatch) {
          try {
            picUrl = decodeURIComponent(picMatch[1].replace(/\\u([0-9a-f]{4})/gi, (match, code) => String.fromCharCode(parseInt(code, 16))));
          } catch (e) {
            picUrl = picMatch[1];
          }
        }
        
        let data = {
          username: username,
          full_name: '',
          id: '',
          profile_pic_url: picUrl
        };

        const followersMatch = html.match(/"edge_followed_by"\s*:\s*\{\s*"count"\s*:\s*(\d+)/);
        if (followersMatch) data.followers = parseInt(followersMatch[1], 10);

        const followingMatch = html.match(/"edge_follow"\s*:\s*\{\s*"count"\s*:\s*(\d+)/);
        if (followingMatch) data.following = parseInt(followingMatch[1], 10);

        const postsMatch = html.match(/"edge_owner_to_timeline_media"\s*:\s*\{\s*"count"\s*:\s*(\d+)/);
        if (postsMatch) data.posts = parseInt(postsMatch[1], 10);

        // Try multiple patterns for biography
        let bioMatch = html.match(/"biography"\s*:\s*"(.*?)"/s);
        if (!bioMatch) {
          bioMatch = html.match(/<meta\s+property="og:description"\s+content="([^"]+)"/i);
        }
        if (bioMatch) {
          try {
            data.biography = bioMatch[1]
              .replace(/\\n/g, ' ')
              .replace(/\\u([0-9a-f]{4})/gi, (match, code) => String.fromCharCode(parseInt(code, 16)))
              .trim();
          } catch (e) {
            data.biography = bioMatch[1].replace(/\\n/g, ' ').trim();
          }
        }

        // Try to get full_name
        const fullNameMatch = html.match(/"full_name"\s*:\s*"([^"]+)"/i);
        if (fullNameMatch) {
          try {
            data.full_name = fullNameMatch[1]
              .replace(/\\u([0-9a-f]{4})/gi, (match, code) => String.fromCharCode(parseInt(code, 16)))
              .trim();
          } catch (e) {
            data.full_name = fullNameMatch[1].trim();
          }
        }

        return data.profile_pic_url ? data : null;
      }
    } catch (htmlError) {
      // Ignore fallback errors
    }
  }

  return null;
}

