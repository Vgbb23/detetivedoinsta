<?php

?>

<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <title>DeepGram | Investigue o perfil de usuários no instagram</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="styles/global.css">
    <script src="js/matrix_effect.js" defer></script>
    <!-- Scale viewport to 100% -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  </head>
  
  <script>
  window.pixelId = "692fa7713a7201fbffd7c0c9";
  var a = document.createElement("script");
  a.setAttribute("async", "");
  a.setAttribute("defer", "");
  a.setAttribute("src", "https://cdn.utmify.com.br/scripts/pixel/pixel.js");
  document.head.appendChild(a);
</script>
   <script
  src="https://cdn.utmify.com.br/scripts/utms/latest.js"
  data-utmify-prevent-xcod-sck
  data-utmify-prevent-subids
  async
  defer
></script>
  
  <body>
    <canvas id="c"></canvas>
    <div class="overlay">
      <div class="card">
        <div class="brand-row">
          <img src="styles/img/image.webp" alt="DeepGram" class="brand-logo" />
        </div>
        <h1 class="text-3xl font-bold mb-4">O que realmente ele(a) faz quando tá no Insta?</h1>
        <p class="sub">Descubra a verdade sobre qualquer pessoa do Instagram. Só com o @.</p>
        <div id="hero-state">
          <a href="#" id="spy-btn" class="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye-icon lucide-eye"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>
            <span>Espionar Agora</span>
          </a>
          <div class="badge-row">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 8V7a5 5 0 0 0-10 0v1H5v12h14V8h-2zm-8 0V7a3 3 0 0 1 6 0v1H9z"></path></svg>
            <span>100% Anônimo. A pessoa <strong>NUNCA</strong> saberá.</span>
          </div>
        </div>
        <div id="form-state" class="hidden">
          <div class="input-row">
            <div class="input-wrapper">
              <span class="input-icon">@</span>
              <input id="ig-handle" type="text" class="input-field" placeholder="Digite o @ da pessoa." inputmode="text" autocapitalize="none" autocomplete="off" />
              <button id="submit-handle" class="input-submit" aria-label="Enviar">
                <svg id="submit-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                <span id="submit-loader" class="spinner hidden"></span>
              </button>
            </div>
          </div>
          <div class="badge-row warn">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <span>Apenas 1 pesquisa por pessoa.</span>
          </div>
        </div>
        <div id="confirm-state" class="hidden confirm-card">
          <div class="confirm-title">Confirmar Pesquisa</div>
          <div class="confirm-sub">Você deseja espionar o perfil <span class="confirm-user" id="confirm-username"></span>?</div>
          <img id="confirm-avatar" class="avatar" src="" alt="Avatar" />
          <div id="confirm-fullname" class="brand-name" style="margin-bottom:6px;"></div>
          <div id="confirm-bio" class="bio"></div>
          <div class="stats">
            <div class="stats-item"><div class="value" id="confirm-posts">0</div><div class="label">Publicações</div></div>
            <div class="stats-item"><div class="value" id="confirm-followers">0</div><div class="label">Seguidores</div></div>
            <div class="stats-item"><div class="value" id="confirm-following">0</div><div class="label">Seguindo</div></div>
          </div>
          <div class="info-panel">
            Nossa plataforma libera somente uma pesquisa por pessoa, então confirme se realmente deseja espionar.
          </div>
          <div class="btn-row">
            <button id="fix-handle" class="btn-outline">Corrigir @</button>
            <button id="confirm-btn" class="btn-gradient">Confirmar <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg></button>
          </div>
        </div>
      </div>
      <div class="footer-note">+<span id="profiles-count">8.569</span> perfis analisados hoje</div>
    </div>

  </body>
  <script defer>
    document.addEventListener('DOMContentLoaded', function() {
      try { localStorage.clear(); } catch (e) {}
      var btn = document.getElementById('spy-btn');
      var hero = document.getElementById('hero-state');
      var form = document.getElementById('form-state');
      var input = document.getElementById('ig-handle');
      var submitBtn = document.getElementById('submit-handle');
      var submitIcon = document.getElementById('submit-icon');
      var submitLoader = document.getElementById('submit-loader');
      var confirmState = document.getElementById('confirm-state');
      var fixBtn = document.getElementById('fix-handle');
      var confirmBtn = document.getElementById('confirm-btn');
      var brandRow = document.querySelector('.brand-row');
      var heroTitle = document.querySelector('h1');
      var heroSub = document.querySelector('p.sub');
      var currentHandle = null;
      var elUsername = document.getElementById('confirm-username');
      var elFullname = document.getElementById('confirm-fullname');
      var elAvatar = document.getElementById('confirm-avatar');
      var elBio = document.getElementById('confirm-bio');
      var elPosts = document.getElementById('confirm-posts');
      var elFollowers = document.getElementById('confirm-followers');
      var elFollowing = document.getElementById('confirm-following');
      if (btn && hero && form) {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          hero.classList.add('hidden');
          form.classList.remove('hidden');
          setTimeout(function(){ if (input) { input.focus(); } }, 50);
        });
      }
      function formatBR(n){ try { return Number(n||0).toLocaleString('pt-BR'); } catch(e){ return String(n||0); } }
      function startLoading(){ if (submitIcon && submitLoader && submitBtn){ submitIcon.classList.add('hidden'); submitLoader.classList.remove('hidden'); submitBtn.disabled = true; } }
      function stopLoading(){ if (submitIcon && submitLoader && submitBtn){ submitLoader.classList.add('hidden'); submitIcon.classList.remove('hidden'); submitBtn.disabled = false; } }
      if (submitBtn) {
        submitBtn.addEventListener('click', function(e){
          e.preventDefault();
          var handle = (input ? input.value.trim() : '').replace(/^@+/, '');
          if (!handle) { if (input) input.focus(); return; }
          startLoading();
          fetch('api/scrape_instagram.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'username=' + encodeURIComponent(handle)
          }).then(function(r){ return r.json(); })
          .then(function(json){
            if (!json || !json.ok) throw new Error((json && json.error) || 'Erro');
            var d = json.data || {};
            try { localStorage.setItem('dg_result', JSON.stringify(d)); } catch(e) {}
            currentHandle = d.username || handle;
            if (elUsername) elUsername.textContent = '@' + (d.username || handle);
            if (elFullname) elFullname.textContent = d.full_name || '';
            if (elAvatar) elAvatar.src = d.profile_pic_url ? ('api/proxy_image.php?url=' + encodeURIComponent(d.profile_pic_url)) : '';
            if (elBio) elBio.textContent = d.biography || '';
            if (elPosts) elPosts.textContent = formatBR(d.posts);
            if (elFollowers) elFollowers.textContent = formatBR(d.followers);
            if (elFollowing) elFollowing.textContent = formatBR(d.following);
            form.classList.add('hidden');
            confirmState.classList.remove('hidden');
            if (brandRow) brandRow.classList.add('hidden');
            if (heroTitle) heroTitle.classList.add('hidden');
            if (heroSub) heroSub.classList.add('hidden');
          }).catch(function(err){
            alert('Não foi possível obter o perfil. Verifique o @ e tente novamente.');
          }).finally(function(){ stopLoading(); });
        });
      }
      if (fixBtn) {
        fixBtn.addEventListener('click', function(){
          confirmState.classList.add('hidden');
          form.classList.remove('hidden');
          setTimeout(function(){ if (input) { input.focus(); } }, 50);
          if (brandRow) brandRow.classList.remove('hidden');
          if (heroTitle) heroTitle.classList.remove('hidden');
          if (heroSub) heroSub.classList.remove('hidden');
        });
      }
      if (confirmBtn) {
        confirmBtn.addEventListener('click', function(){
          var h = currentHandle || (elUsername ? String(elUsername.textContent||'').replace(/^@+/, '') : '');
          if (!h) { h = input ? input.value.replace(/^@+/, '') : ''; }
          location.href = 'ig_login.php?u=' + encodeURIComponent(h);
        });
      }
      var counterEl = document.getElementById('profiles-count');
      if (counterEl) {
        var value = parseInt(counterEl.textContent.replace(/\D/g, ''), 10) || 0;
        function tick(){
          var inc = Math.floor(Math.random() * 21) + 3;
          value += inc;
          counterEl.textContent = value.toLocaleString('pt-BR');
          var next = Math.floor(Math.random() * 3000) + 2000;
          setTimeout(tick, next);
        }
        setTimeout(tick, Math.floor(Math.random() * 2000) + 1000);
      }
    });
  </script>
</html>
