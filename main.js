document.addEventListener('DOMContentLoaded', () => {

  const hamburger = document.getElementById('hamburger');
  const navMenu   = document.getElementById('nav-menu');
  hamburger?.addEventListener('click', () => {
    hamburger.classList.toggle('open');
    navMenu.classList.toggle('open');
  });

  const animEls = document.querySelectorAll('.animate-on-scroll');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((e, i) => {
      if (e.isIntersecting) {
        setTimeout(() => e.target.classList.add('visible'), i * 80);
        observer.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });
  animEls.forEach(el => observer.observe(el));

  const sections = document.querySelectorAll('section[id]');
  window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;
    sections.forEach(s => {
      const top = s.offsetTop - 100;
      const h   = s.offsetHeight;
      const id  = s.getAttribute('id');
      const link = document.querySelector(`.nav-link[href="#${id}"]`);
      if (link) link.classList.toggle('active', scrollY >= top && scrollY < top + h);
    });
  });

  const filterBtns = document.querySelectorAll('.filter-btn');
  const cards = document.querySelectorAll('.card-wisata');

  filterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      filterBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const kat = btn.dataset.kategori;
      cards.forEach(card => {
        const match = kat === 'semua' || card.dataset.kategori === kat;
        card.style.display = match ? 'block' : 'none';
        if (match) card.style.animation = 'fadeInUp .4s ease';
      });
    });
  });

  const searchInput = document.getElementById('search-input');
  searchInput?.addEventListener('input', () => {
    const q = searchInput.value.toLowerCase();
    cards.forEach(card => {
      const nama = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
      const lok  = card.querySelector('.card-lokasi')?.textContent.toLowerCase() || '';
      card.style.display = (nama.includes(q) || lok.includes(q)) ? 'block' : 'none';
    });
  });

  const searchBtn = document.getElementById('search-btn');
  searchBtn?.addEventListener('click', () => {
    const q = document.getElementById('search-input').value.toLowerCase();
    cards.forEach(card => {
      const nama = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
      card.style.display = nama.includes(q) ? 'block' : 'none';
    });
  });

  const modal = document.getElementById('modal-wisata');
  const modalClose = document.getElementById('modal-close');

  document.querySelectorAll('[data-open-modal]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.dataset.openModal;
      // Fetch detail via AJAX
      fetch(`ajax/get_wisata.php?id=${id}`)
        .then(r => r.json())
        .then(data => {
          if (!data.success) return;
          const w = data.wisata;
          const imgSrc = w.gambar ? `uploads/wisata/${w.gambar}` : 'assets/images/default.jpg';

          document.getElementById('modal-img').src = imgSrc;
          document.getElementById('modal-img').alt = w.nama;
          document.getElementById('modal-nama').textContent = w.nama;
          document.getElementById('modal-kategori').textContent = w.kategori;
          document.getElementById('modal-lokasi').textContent = w.lokasi;
          document.getElementById('modal-harga').textContent = w.harga_tiket;
          document.getElementById('modal-jam').textContent = w.jam_buka;
          document.getElementById('modal-rating').textContent = '⭐ ' + w.rating;
          document.getElementById('modal-desc').textContent = w.deskripsi;
          if (w.maps_url) {
            document.getElementById('modal-maps').href = w.maps_url;
            document.getElementById('modal-maps').style.display = '';
          } else {
            document.getElementById('modal-maps').style.display = 'none';
          }
          modal.classList.add('active');
          document.body.style.overflow = 'hidden';
        });
    });
  });

  modalClose?.addEventListener('click', closeModal);
  modal?.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

  function closeModal() {
    modal?.classList.remove('active');
    document.body.style.overflow = '';
  }

  function animateCounter(el, target, duration = 1500) {
    let start = 0;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
      start += step;
      if (start >= target) { el.textContent = target + '+'; clearInterval(timer); }
      else el.textContent = Math.floor(start) + '+';
    }, 16);
  }

  const counters = document.querySelectorAll('[data-count]');
  const countObserver = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        animateCounter(e.target, parseInt(e.target.dataset.count));
        countObserver.unobserve(e.target);
      }
    });
  });
  counters.forEach(c => countObserver.observe(c));

});
