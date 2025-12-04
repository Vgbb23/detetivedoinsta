export default async function handler(req, res) {
  const url = req.query?.url || '';
  
  if (!url || typeof url !== 'string') {
    return res.status(400).json({ ok: false, error: 'invalid_url' });
  }

  try {
    const urlObj = new URL(url);
    const host = urlObj.hostname;
    const allowed = ['fbcdn.net', 'cdninstagram.com', 'instagram.com'];
    
    let ok = false;
    for (const domain of allowed) {
      if (host === domain || host.endsWith('.' + domain)) {
        ok = true;
        break;
      }
    }

    if (!ok || urlObj.protocol !== 'https:') {
      return res.status(403).json({ ok: false, error: 'forbidden' });
    }

    const imageResponse = await fetch(url, {
      headers: {
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Referer': 'https://www.instagram.com/'
      }
    });

    if (!imageResponse.ok || imageResponse.status >= 400) {
      return res.status(502).json({ ok: false, error: 'upstream_error' });
    }

    const contentType = imageResponse.headers.get('content-type') || 'image/jpeg';
    
    if (!contentType.startsWith('image/')) {
      return res.status(502).json({ ok: false, error: 'upstream_error' });
    }

    const imageBuffer = await imageResponse.arrayBuffer();
    
    res.setHeader('Content-Type', contentType);
    res.setHeader('Cache-Control', 'public, max-age=3600');
    return res.send(Buffer.from(imageBuffer));
  } catch (error) {
    return res.status(502).json({ ok: false, error: 'upstream_error' });
  }
}

