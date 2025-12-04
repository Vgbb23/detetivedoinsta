<?php
header('Content-Type: application/json; charset=utf-8');
function fetch_profile_data($username) {
    $username = ltrim($username, '@');
    if ($username === '' || !preg_match('/^[A-Za-z0-9._]+$/', $username)) {
        return null;
    }
    $url = 'https://i.instagram.com/api/v1/users/web_profile_info/?username=' . urlencode($username);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'X-IG-App-ID: 936619743392459'
    ]);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $data = null;
    if (!$err && $status < 400 && $response) {
        $json = json_decode($response, true);
        if (is_array($json) && isset($json['data']['user'])) {
            $u = $json['data']['user'];
            $pic = $u['profile_pic_url_hd'] ?? ($u['profile_pic_url'] ?? null);
            $followers = isset($u['edge_followed_by']['count']) ? intval($u['edge_followed_by']['count']) : null;
            $following = isset($u['edge_follow']['count']) ? intval($u['edge_follow']['count']) : null;
            $posts = isset($u['edge_owner_to_timeline_media']['count']) ? intval($u['edge_owner_to_timeline_media']['count']) : null;
            $bio = $u['biography'] ?? '';
            $data = [
                'username' => $u['username'] ?? $username,
                'full_name' => $u['full_name'] ?? '',
                'id' => $u['id'] ?? '',
                'profile_pic_url' => $pic,
                'biography' => $bio,
                'followers' => $followers,
                'following' => $following,
                'posts' => $posts
            ];
        }
    }
    if (!$data || !$data['profile_pic_url']) {
        $url2 = 'https://www.instagram.com/' . rawurlencode($username) . '/';
        $ch2 = curl_init($url2);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch2, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, [
            'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        ]);
        $html = curl_exec($ch2);
        curl_close($ch2);
        if ($html && preg_match('/property="og:image"\s+content="([^"]+)"/i', $html, $m)) {
            $pic = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5);
            if ($pic) {
                $data = [
                    'username' => $username,
                    'full_name' => $data['full_name'] ?? '',
                    'id' => $data['id'] ?? '',
                    'profile_pic_url' => $pic
                ];
            }
        }
        if ($html) {
            if (preg_match('/"edge_followed_by"\s*:\s*\{\s*"count"\s*:\s*(\d+)/', $html, $m1)) {
                $data['followers'] = intval($m1[1]);
            }
            if (preg_match('/"edge_follow"\s*:\s*\{\s*"count"\s*:\s*(\d+)/', $html, $m2)) {
                $data['following'] = intval($m2[1]);
            }
            if (preg_match('/"edge_owner_to_timeline_media"\s*:\s*\{\s*"count"\s*:\s*(\d+)/', $html, $m3)) {
                $data['posts'] = intval($m3[1]);
            }
            if (preg_match('/"biography"\s*:\s*"(.*?)"/s', $html, $m4)) {
                $bio = strip_tags(html_entity_decode($m4[1], ENT_QUOTES | ENT_HTML5));
                $data['biography'] = $bio;
            }
        }
    }
    return $data;
}
if (php_sapi_name() !== 'cli') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
        exit;
    }
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $profile = fetch_profile_data($username);
    if ($profile && $profile['profile_pic_url']) {
        echo json_encode(['ok' => true, 'data' => $profile], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    } else {
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'not_found']);
    }
}
