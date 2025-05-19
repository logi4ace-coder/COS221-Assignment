document.addEventListener("DOMContentLoaded", function (){

        const darkButton = document.getElementById('theme-Dack');
        darkButton.addEventListener('click', function() {
            document.body.classList.add('dark-theme');
            localStorage.setItem('theme', 'dark');
        });
    
        const lightButton = document.getElementById('theme-Light');
        lightButton.addEventListener('click', function() {
            document.body.classList.remove('dark-theme');
            localStorage.setItem('theme', 'light');
        });
    
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
        }
});

