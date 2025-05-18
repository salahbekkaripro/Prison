// ✅ transition.js avec rechargement JS dynamique (validate-post, etc.)
document.addEventListener('DOMContentLoaded', () => {
  const transition = document.getElementById('page-transition');
  const app = document.getElementById('app');

  function waitForRegisterScript(retries = 10) {
    const rawScript = document.getElementById("register-script-content");
    if (!rawScript) {
      if (retries > 0) {
        setTimeout(() => waitForRegisterScript(retries - 1), 100);
      }
      return;
    }

    if (!rawScript.textContent.includes("initRegisterPage")) return;

    eval(rawScript.textContent);

    let delay = 0;
    function tryInitCall(retry = 5) {
      setTimeout(() => {
        if (typeof initRegisterPage === "function") {
          initRegisterPage();
        } else if (retry > 0) {
          delay += 25;
          tryInitCall(retry - 1);
        }
      }, 25);
    }

    tryInitCall();
  }

  function startTransitionOut() {
    const audio = new Audio('/forum-prison/assets/sounds/transition.mp3');
    audio.volume = 0.3;
    audio.play().catch(() => {});
    document.body.classList.add('transition-out');
    transition.style.display = 'block';

    const glow = document.getElementById('glow-border-overlay');
    if (glow) {
      glow.style.transition = 'none';
      glow.style.opacity = '0.7';
      setTimeout(() => {
        glow.style.transition = 'opacity 1.5s ease';
        glow.style.opacity = '0';
      }, 2500);
    }
  }

  function endTransitionIn() {
    document.body.classList.remove('transition-out');
    transition.style.display = 'none';
  }

  function isIndex(href) {
    return href.includes('index.php') || href === '/' || href.endsWith('/index.php');
  }

  function loadQuotesIfNeeded(href) {
    if (isIndex(href) && typeof initQuotes === 'function') {
      setTimeout(initQuotes, 0);
    }
  }

  document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href]');
    if (!link) return;

    const url = link.getAttribute('href');
    if (
      url.startsWith('#') ||
      url.startsWith('javascript') ||
      url.includes('logout') ||
      link.hasAttribute('download') ||
      link.target === '_blank'
    ) return;

    e.preventDefault();
    startTransitionOut();

    setTimeout(() => {
      fetch(url)
        .then(res => res.text())
        .then(html => {
          const parser = new DOMParser();
          const doc = parser.parseFromString(html, 'text/html');
          const newContent = doc.getElementById('app');

          if (newContent) {
            app.innerHTML = newContent.innerHTML;

            // ⚡ Recharge tous les <script> internes
            app.querySelectorAll("script").forEach(oldScript => {
              const newScript = document.createElement("script");
              if (oldScript.src) newScript.src = oldScript.src;
              else newScript.textContent = oldScript.textContent;
              oldScript.replaceWith(newScript);
            });

            // ✅ RECHARGEMENT DYNAMIQUE du script validate-post.js
            const currentPath = new URL(url, window.location.origin).pathname;
            if (currentPath.endsWith('validate_post.php')) {
              const existing = document.querySelector('script[src*="validate-post.js"]');
              if (existing) existing.remove(); // on recharge

              const script = document.createElement('script');
              script.src = '/forum-prison/assets/js/validate-post.js';
              script.onload = () => {
                if (typeof initValidatePostPage === "function") {
                  console.log("✅ initValidatePostPage chargé après transition");
                  initValidatePostPage();
                }
              };
              document.body.appendChild(script);
            }
            if (currentPath.endsWith('notifications.php')) {
              const existing = document.querySelector('script[src*="notifications.js"]');
              if (existing) existing.remove(); // éviter double exécution
            
              const script = document.createElement('script');
              script.src = '/forum-prison/assets/js/notifications.js';
              script.onload = () => {
                if (typeof initNotificationsPage === "function") {
                  console.log("✅ initNotificationsPage rechargé après transition");
                  initNotificationsPage();
                }
              };
              document.body.appendChild(script);
            }
            

            // Cas des scripts dynamiques (register)
            const rawScript = newContent.querySelector('#register-script-content');
            if (rawScript) {
              const injectedScript = document.createElement('script');
              injectedScript.id = 'register-script-content';
              injectedScript.type = 'text/template';
              injectedScript.textContent = rawScript.textContent;
              document.body.appendChild(injectedScript);
              setTimeout(waitForRegisterScript, 50);
            }

            history.pushState(null, '', url);
            loadQuotesIfNeeded(url);
          } else {
            window.location.href = url;
          }
        })
        .catch(() => window.location.href = url)
        .finally(() => endTransitionIn());
    }, 400);
  });

  window.addEventListener('popstate', () => {
    fetch(location.href)
      .then(res => res.text())
      .then(html => {
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContent = doc.getElementById('app');
        if (newContent) {
          app.innerHTML = newContent.innerHTML;

          app.querySelectorAll("script").forEach(oldScript => {
            const newScript = document.createElement("script");
            if (oldScript.src) newScript.src = oldScript.src;
            else newScript.textContent = oldScript.textContent;
            oldScript.replaceWith(newScript);
          });

          setTimeout(() => {
            if (typeof initValidatePostPage === "function") initValidatePostPage();
          }, 0);

          loadQuotesIfNeeded(location.href);
        } else {
          window.location.reload();
        }
      });
  });

  setTimeout(() => {
    transition.style.display = 'none';
  }, 400);

  loadQuotesIfNeeded(location.href);
  setTimeout(waitForRegisterScript, 50);
});
