<?php
require_once __DIR__ . '/inc/auth.php';
require_login();
require_once __DIR__ . '/inc/csrf.php'; csrf_check();
include __DIR__ . '/inc/header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Übersetzer</h1>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card p-3">
      <form id="formTranslate" method="post">
        <?php csrf_field(); ?>
        <div class="row g-2 align-items-end">
          <div class="col">
            <label class="form-label">Quelle</label>
            <select class="form-select" name="source" id="src">
              <option value="auto">Auto</option>
              <option value="de">Deutsch</option>
              <option value="en">Englisch</option>
              <option value="tr">Türkisch</option>
              <option value="fr">Französisch</option>
              <option value="es">Spanisch</option>
            </select>
          </div>
          <div class="col">
            <label class="form-label">Ziel</label>
            <select class="form-select" name="target" id="tgt">
              <option value="de">Deutsch</option>
              <option value="en" selected>Englisch</option>
              <option value="tr">Türkisch</option>
              <option value="fr">Französisch</option>
              <option value="es">Spanisch</option>
            </select>
          </div>
          <div class="col-auto">
            <button class="btn btn-violet" id="btnTranslate">Übersetzen</button>
          </div>
        </div>
        <div class="mt-3">
          <label class="form-label">Text (HTML erlaubt)</label>
          <textarea class="form-control" rows="8" id="txtSrc" name="q" placeholder="Text hier einfügen..."></textarea>
        </div>
      </form>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card p-3">
      <label class="form-label">Ergebnis</label>
      <textarea class="form-control" rows="12" id="txtOut" readonly></textarea>
      <div class="mt-2 d-flex gap-2">
        <button class="btn btn-outline-light" id="copyOut">Kopieren</button>
      </div>
    </div>
  </div>
</div>

<hr class="my-4">

<h2 class="h5 mb-3">Seiten automatisch übersetzen</h2>
<p class="text-secondary">Erstellt aus einer existierenden Seite eine neue Seite in der Zielsprache (Slug wird mit Sprache erweitert).</p>
<div class="card p-3">
  <form id="formPageTranslate" method="post">
    <?php csrf_field(); ?>
    <div class="row g-2 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Seite</label>
        <select class="form-select" id="pageId" name="page_id" required>
          <?php
            foreach($pdo->query('SELECT id, title, slug FROM pages WHERE deleted_at IS NULL ORDER BY updated_at DESC') as $p){
              echo "<option value='{$p['id']}'>".htmlspecialchars($p['title'])." (".$p['slug'].")</option>";
            }
          ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Ziel</label>
        <select class="form-select" id="pageTarget">
          <option value="en" selected>Englisch</option>
          <option value="de">Deutsch</option>
          <option value="tr">Türkisch</option>
          <option value="fr">Französisch</option>
          <option value="es">Spanisch</option>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-violet" id="btnPageTranslate">Neue Übersetzungs-Seite anlegen</button>
      </div>
    </div>
  </form>
</div>

<script>
document.getElementById('btnTranslate').addEventListener('click', async (e)=>{
  e.preventDefault();
  const form = new FormData(document.getElementById('formTranslate'));
  const res = await fetch('api_translate.php', {method:'POST', body:form});
  const data = await res.json();
  document.getElementById('txtOut').value = data.translatedText || (data.error || 'Fehler');
});
document.getElementById('copyOut').addEventListener('click', ()=>{
  const t = document.getElementById('txtOut'); t.select(); document.execCommand('copy');
});
document.getElementById('btnPageTranslate').addEventListener('click', async (e)=>{
  e.preventDefault();
  const pid = document.getElementById('pageId').value;
  const tgt = document.getElementById('pageTarget').value;
  // Hole Seite
  const fd = new FormData(); fd.append('q',''); fd.append('target', tgt); fd.append('source','auto'); // csrf in session
  // per PHP ziehen wir die Seite
  const resp = await fetch('translate_page_action.php?page_id='+pid+'&target='+tgt, {method:'POST', body:fd});
  const data = await resp.json();
  alert(data.ok ? 'Seite erstellt: '+data.slug : ('Fehler: '+data.error));
});
</script>

<?php include __DIR__ . '/inc/footer.php'; ?>