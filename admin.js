document.addEventListener('DOMContentLoaded', () => {

  const mobToggle = document.getElementById('mob-toggle');
  const sidebar   = document.getElementById('sidebar');
  mobToggle?.addEventListener('click', () => sidebar?.classList.toggle('open'));

  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 && sidebar?.classList.contains('open')) {
      if (!sidebar.contains(e.target) && e.target !== mobToggle) {
        sidebar.classList.remove('open');
      }
    }
  });

  const animEls = document.querySelectorAll('.animate-on-scroll');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if (e.isIntersecting) {
        setTimeout(() => e.target.style.cssText += 'opacity:1;transform:translateY(0)', i * 60);
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.1 });
  animEls.forEach(el => {
    el.style.cssText = 'opacity:0; transform:translateY(20px); transition:opacity .5s ease, transform .5s ease;';
    observer.observe(el);
  });

  const flashMsgs = document.querySelectorAll('.flash-msg');
  flashMsgs.forEach(msg => {
    setTimeout(() => {
      msg.style.transition = 'opacity .5s ease';
      msg.style.opacity = '0';
      setTimeout(() => msg.remove(), 500);
    }, 4000);
  });

  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', function(e) {
      if (!confirm(this.dataset.confirm || 'Yakin?')) e.preventDefault();
    });
  });

  const fileInput = document.getElementById('gambar-input');
  fileInput?.addEventListener('change', function () {
    const wrap    = document.getElementById('preview-wrap');
    const preview = document.getElementById('img-preview');
    if (this.files && this.files[0]) {
      const reader = new FileReader();
      reader.onload = e => {
        if (preview) preview.src = e.target.result;
        if (wrap) wrap.style.display = 'block';
      };
      reader.readAsDataURL(this.files[0]);
    }
  });

  document.querySelectorAll('.stat-num').forEach(el => {
    const target = parseInt(el.textContent);
    if (!isNaN(target) && target > 0) {
      let count = 0;
      const step = Math.ceil(target / 30);
      const timer = setInterval(() => {
        count = Math.min(count + step, target);
        el.textContent = count;
        if (count >= target) clearInterval(timer);
      }, 30);
    }
  });
});
