const setting = document.getElementById('menu');
const smallMenu = document.querySelector('.small-menu');

// اظهار او اخفاء القائمة عند الضغط على "setting"
setting.querySelector('a').addEventListener('click', (event) => {
    event.preventDefault(); // منع التنقل الافتراضي فقط لرابط "setting"
    smallMenu.style.display = smallMenu.style.display === 'block' ? 'none' : 'block';
});

// اخفاء القائمة عند الضغط في أي مكان خارجها
document.addEventListener('click', (event) => {
    if (!setting.contains(event.target)) {
        smallMenu.style.display = 'none';
    }
});