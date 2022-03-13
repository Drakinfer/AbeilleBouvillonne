const btnresponsive = document.querySelector('.user-burger');
const nav = document.querySelector('.user-nav');

btnresponsive.onclick = function navUser() {
    nav.classList.toggle('nav-active');
    btnresponsive.classList.toggle('active');
}

const btnresponsiveadmin = document.querySelector('.admin-burger');
const navadmin = document.querySelector('.admin-nav');

btnresponsiveadmin.onclick = function navAdmin() {
    navadmin.classList.toggle('nav-active');
    btnresponsiveadmin.classList.toggle('active');
}