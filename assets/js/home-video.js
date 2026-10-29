/* The component remounts the video after language or section changes. */
(() => {
  const ready = new WeakSet();
  function mount() {
    document.querySelectorAll('video.bng-home-video').forEach(video => {
      if (ready.has(video)) return;
      ready.add(video);
      video.muted = video.defaultMuted = true;
      video.volume = 0;
      video.autoplay = video.loop = video.playsInline = true;
      video.controls = false;
      const play = () => { const attempt = video.play(); if (attempt) attempt.catch(() => {}); };
      video.addEventListener('loadeddata', play, {once:true});
      play();
    });
  }
  mount();
  new MutationObserver(mount).observe(document.documentElement, {childList:true, subtree:true});
})();
