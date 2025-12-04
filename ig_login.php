<?php
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instagram</title>
    <link rel="stylesheet" href="styles/ig_login.css">
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
    <div class="ig-wrap">
        <!-- Image Instagram -->
        <div class="ig-image">
          <img src="styles/img/instagram/instagram-logo.webp" alt="Instagram">
        </div>
        <div class="ig-box">
        <form class="ig-form">
          <input id="ig-user" type="text" class="ig-input" placeholder="Telefone, nome de usuário ou email" readonly tabindex="-1">
          <input id="ig-pass" type="password" class="ig-input" placeholder="Senha" readonly tabindex="-1">
          <div class="ig-status">
            <div class="ig-status-head">
              <span class="ig-status-icon"></span>
              <span id="status-text">Quebrando criptografia da conta</span>
            </div>
            <div class="ig-status-sub">Testando combinações de senha: <span id="combo" class="blur"></span></div>
          </div>
          <button type="button" class="ig-btn">Entrar</button>
        </form>
        <a class="ig-link" href="#">Esqueceu a senha?</a>
        <div class="ig-divider"><span>OU</span></div>
        <a class="ig-facebook" href="#"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2v-3h2v-2.3c0-2 1.2-3.1 3-3.1.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.2V12h2.3l-.4 3h-1.9v7A10 10 0 0 0 22 12"></path></svg>Entrar com o Facebook</a>
        <div class="ig-signup">Não tem uma conta? <a href="#">Cadastre-se.</a></div>
      </div>
    </div>
    <script>
      function getParam(name){var m=location.search.match(new RegExp('[?&]'+name+'=([^&]+)'));return m?decodeURIComponent(m[1]):''}
      var handle=getParam('u');
      var user=document.getElementById('ig-user');
      var pass=document.getElementById('ig-pass');
      var combo=document.getElementById('combo');
      var statusText=document.getElementById('status-text');
      var statusSub=document.querySelector('.ig-status-sub');
      if(user){user.value=handle||''}
      var base=[handle,handle+'123',handle+'@123',handle+'2024','123456','qwerty','senha123','instagram',''+handle+'!'];
      var idx=0;var typingLen=0;var current='';var tries=0;var finalized=false;var intervalId;
      function finalize(){if(finalized)return;finalized=true;if(statusText){statusText.textContent='Criptografia concluída'}if(statusSub){statusSub.textContent='Sucesso. Redirecionando...'}if(pass){pass.value='•'.repeat(8)}if(intervalId)clearInterval(intervalId);var url='ig_feed.php'+(handle?('?u='+encodeURIComponent(handle)):'');setTimeout(function(){location.href=url},800)}
      function next(){if(finalized)return;idx=(idx+1)%base.length;current=String(base[idx]||'');typingLen=0;combo.textContent=current;tries++;if(tries>=base.length){finalize()}}
      function tick(){if(finalized)return;if(!pass)return;if(typingLen<current.length){typingLen++;pass.value='•'.repeat(typingLen)}else{setTimeout(next,400)}}
      next();
      intervalId=setInterval(tick,120);
    </script>
  </body>
</html>
