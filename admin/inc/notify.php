<?php
// inc/notify.php — simple mail() wrapper
function notify_new_page($title, $slug) {
  $to = getenv('ADMIN_EMAIL') ?: 'admin@example.com';
  $subject = "Neue Seite erstellt: $title";
  $link = (isset($_SERVER['HTTP_HOST']) ? 'http://'.$_SERVER['HTTP_HOST'] : '') . "/portfolio_with_backend/page.php?slug=" . urlencode($slug);
  $msg = "Es wurde eine neue Seite erstellt.\n\nTitel: $title\nSlug: $slug\nLink: $link\n";
  @mail($to, $subject, $msg);
}