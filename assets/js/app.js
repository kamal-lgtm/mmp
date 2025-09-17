document.addEventListener('DOMContentLoaded',()=>{
  // Filters
  const filters=document.querySelectorAll('.filters a');
  const cards=document.querySelectorAll('.card');
  filters.forEach(f=>f.addEventListener('click',e=>{
    e.preventDefault();
    filters.forEach(x=>x.classList.remove('active'));
    f.classList.add('active');
    const cat=f.dataset.filter;
    cards.forEach(c=>{
      if(cat==='all'||c.dataset.category===cat){c.style.display='block';}
      else{c.style.display='none';}
    });
  }));

  // Lightbox
  const triggers=document.querySelectorAll('.lightbox-trigger');
  const lightbox=document.getElementById('lightbox');
  const content=lightbox.querySelector('.lightbox-content');
  const close=lightbox.querySelector('.close');
  const prev=lightbox.querySelector('.prev');
  const next=lightbox.querySelector('.next');
  let items=[]; let index=0;

  function open(i){
    index=i; const el=items[i]; const type=el.dataset.type; const src=el.dataset.src;
    content.innerHTML='';
    if(type==='image'){const img=document.createElement('img');img.src=src;content.appendChild(img);}
    if(type==='video'){const vid=document.createElement('video');vid.src=src;vid.controls=true;vid.autoplay=true;content.appendChild(vid);}
    if(type==='youtube'){const iframe=document.createElement('iframe');iframe.src=`https://www.youtube.com/embed/${src}?autoplay=1`;iframe.allowFullscreen=true;content.appendChild(iframe);}
    lightbox.style.display='flex';
  }
  function closeLb(){lightbox.style.display='none';content.innerHTML='';}
  function showNext(){open((index+1)%items.length);}
  function showPrev(){open((index-1+items.length)%items.length);}

  triggers.forEach((t,i)=>{t.addEventListener('click',()=>{items=Array.from(triggers);open(i);});});
  close.addEventListener('click',closeLb);
  next.addEventListener('click',showNext);
  prev.addEventListener('click',showPrev);
  window.addEventListener('keydown',e=>{
    if(lightbox.style.display==='flex'){
      if(e.key==='Escape')closeLb();
      if(e.key==='ArrowRight')showNext();
      if(e.key==='ArrowLeft')showPrev();
    }
  });
});
