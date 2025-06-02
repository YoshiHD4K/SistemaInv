function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('collapsed');
    document.querySelector('.main-content').classList.toggle('collapsed');
}

function showScreen(screenId, link) {
    document.querySelectorAll('.pantalla').forEach(function(sec) {
        sec.classList.remove('active');
    });
    document.getElementById(screenId).classList.add('active');
    document.querySelectorAll('.sidebar ul li a').forEach(function(a) {
        a.classList.remove('active');
    });
    link.classList.add('active');
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('pantalla-proveedores').classList.add('active');
    document.querySelectorAll('.sidebar ul li a[data-screen]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            showScreen(this.getAttribute('data-screen'), this);
        });
    });
});