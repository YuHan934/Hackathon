// ===== Theme Toggle =====
const themeToggle = document.getElementById('themeToggle');
let isBobaTheme = localStorage.getItem('theme') !== 'classic';

function applyTheme() {
  if(isBobaTheme){
    document.documentElement.style.setProperty('--bg-color','#FFE6B3');
    document.documentElement.style.setProperty('--text-color','#4A2C2A');
    document.documentElement.style.setProperty('--card-bg','#FFDAB3');
    document.documentElement.style.setProperty('--navbar-bg','#FFAD60');
    document.documentElement.style.setProperty('--button-bg','#FF8050');
    document.documentElement.style.setProperty('--button-text','#fff');
    if(themeToggle) themeToggle.textContent='🌙 Change Theme';
    localStorage.setItem('theme','boba');
  } else {
    document.documentElement.style.setProperty('--bg-color','#f7f9fc');
    document.documentElement.style.setProperty('--text-color','#2C3E50');
    document.documentElement.style.setProperty('--card-bg','#ffffff');
    document.documentElement.style.setProperty('--navbar-bg','#2C3E50');
    document.documentElement.style.setProperty('--button-bg','#3498DB');
    document.documentElement.style.setProperty('--button-text','#fff');
    if(themeToggle) themeToggle.textContent='☀️ Boba Theme';
    localStorage.setItem('theme','classic');
  }
}
applyTheme();
if(themeToggle) themeToggle.addEventListener('click',()=>{isBobaTheme=!isBobaTheme;applyTheme();});

// ===== Carousel Auto-Swipe =====
const wrapper = document.getElementById('featureWrapper');
if(wrapper){
  const cards = wrapper.children;
  let index = 0;

  const dotsContainer = document.getElementById('carouselDots');
  if(dotsContainer){
    for(let i=0;i<cards.length;i++){
      const dot = document.createElement('span');
      dot.addEventListener('click',()=>{index=i;updateCarousel();});
      dotsContainer.appendChild(dot);
    }
  }

  function updateCarousel(){
    wrapper.style.transform=`translateX(-${index*100}%)`;
    if(dotsContainer){
      Array.from(dotsContainer.children).forEach((dot,i)=>{dot.classList.toggle('active',i===index);});
    }
  }
  updateCarousel();
  setInterval(()=>{index=(index+1)%cards.length;updateCarousel();},5000);
}

// ===== Boba Parallax =====
const circles=document.querySelectorAll('.boba-circle');
document.addEventListener('mousemove',(e)=>{
  const x=(e.clientX/window.innerWidth-0.5)*30;
  const y=(e.clientY/window.innerHeight-0.5)*30;
  circles.forEach((circle,i)=>{
    const speed=(i+1)*0.3;
    circle.style.transform=`translate3d(${x*speed}px,${y*speed}px,0)`;
  });
});
