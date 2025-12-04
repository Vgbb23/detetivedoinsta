export default async function handler(req, res) {
  if (req.method !== 'POST') {
    return res.status(405).json({ ok: false, error: 'method_not_allowed' });
  }

  const username = (req.body?.username || '').trim().replace(/^@+/, '');
  
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
    return res.status(500).json({ ok: false, error: 'server_error' });
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
        const picMatch = html.match(/property="og:image"\s+content="([^"]+)"/i);
        
        let data = {
          username: username,
          full_name: '',
          id: '',
          profile_pic_url: picMatch ? decodeURIComponent(picMatch[1]) : null
        };

        const followersMatch = html.match(/"edge_followed_by"\s*:\s*\{\s*"count"\s*:\s*(\d+)/);
        if (followersMatch) data.followers = parseInt(followersMatch[1], 10);

        const followingMatch = html.match(/"edge_follow"\s*:\s*\{\s*"count"\s*:\s*(\d+)/);
        if (followingMatch) data.following = parseInt(followingMatch[1], 10);

        const postsMatch = html.match(/"edge_owner_to_timeline_media"\s*:\s*\{\s*"count"\s*:\s*(\d+)/);
        if (postsMatch) data.posts = parseInt(postsMatch[1], 10);

        const bioMatch = html.match(/"biography"\s*:\s*"(.*?)"/s);
        if (bioMatch) {
          data.biography = bioMatch[1].replace(/\\n/g, ' ').trim();
        }

        return data.profile_pic_url ? data : null;
      }
    } catch (htmlError) {
      // Ignore fallback errors
    }
  }

  return null;
}

