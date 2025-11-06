<?php
require_once __DIR__ . '/config.php';
session_start();
require_once __DIR__ . '/admin/inc/translate_cfg.php';
require_once __DIR__ . '/admin/inc/csrf.php'; csrf_check();
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Übersetzer</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body>
<?php include __DIR__ . '/public_nav.php'; ?>
<main class="container my-4">
<h1>Übersetzer</h1>
<form id="tform" method="post">
  <?php csrf_field(); ?>
  <div class="row g-2">
    <div class="col-md-3"><select class="form-select" name="source"><option value="auto">Auto</option><option value="de">Deutsch</option><option value="en">Englisch</option><option value="tr">Türkisch</option></select></div>
    <div class="col-md-3"><select class="form-select" name="target"><option value="de">Deutsch</option><option value="en" selected>Englisch</option><option value="tr">Türkisch</option></select></div>
    <div class="col-md-3"><button class="btn btn-primary">Übersetzen</button></div>
  </div>
  <div class="mt-3"><textarea class="form-control" rows="6" name="q" placeholder="Text..."></textarea></div>
</form>
<?php
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $url = rtrim($TRANSLATE_API_ENDPOINT,'/').'/translate';
  $data = ['q'=>$_POST['q']??'', 'source'=>$_POST['source']??'auto', 'target'=>$_POST['target']??'en', 'format'=>'text'];
  if ($TRANSLATE_API_KEY!=='') $data['api_key']=$TRANSLATE_API_KEY;
  $ch = curl_init($url);
  curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true, CURLOPT_POST=>true, CURLOPT_POSTFIELDS=>http_build_query($data)]);
  $resp=curl_exec($ch); $err=curl_error($ch); curl_close($ch);
  $js = json_decode($resp,true);
  echo '<div class="card p-3 mt-3"><h5>Ergebnis</h5><pre>'.htmlspecialchars($js['translatedText'] ?? ($err ?: 'Fehler')).'</pre></div>';
}
?>
</main></body></html>