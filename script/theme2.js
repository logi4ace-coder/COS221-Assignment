document.addEventListener('DOMContentLoaded', () => {
  const darkButton = document.getElementById('theme-Dack');
  const lightButton = document.getElementById('theme-Light');
  

  const savedTheme = localStorage.getItem('theme') || 'light';
  if (savedTheme === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
  }
  

  darkButton.addEventListener('click', () => {
    document.documentElement.setAttribute('data-theme', 'dark');
    localStorage.setItem('theme', 'dark');
  });
  

  lightButton.addEventListener('click', () => {
    document.documentElement.removeAttribute('data-theme');
    localStorage.setItem('theme', 'light');
  });
});