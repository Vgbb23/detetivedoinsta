<?php
$url = isset($_GET['url']) ? trim($_GET['url']) : '';
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'invalid_url']);
    exit;
}
$parts = parse_url($url);
$host = $parts['host'] ?? '';
$allowed = ['fbcdn.net', 'cdninstagram.com', 'instagram.com'];
$ok = false;
foreach ($allowed as $d) {
    if ($host === $d || (strlen($host) > strlen($d) + 1 && substr($host, -strlen($d)) === $d)) {
        $ok = true;
        break;
    }
}
if (!$ok || ($parts['scheme'] ?? '') !== 'https') {
    http_response_code(403);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'forbidden']);
    exit;
}
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Referer: https://www.instagram.com/'
]);
$resp = curl_exec($ch);
$err = curl_error($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$ctype = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
curl_close($ch);
if ($err || $status >= 400 || !$resp) {
    http_response_code(502);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'error' => 'upstream_error']);
    exit;
}
if (!$ctype || stripos($ctype, 'image/') !== 0) {
    $ctype = 'image/jpeg';
}
header('Content-Type: ' . $ctype);
header('Cache-Control: public, max-age=3600');
echo $resp;
