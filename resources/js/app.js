import './bootstrap';
import jquery from 'jquery';
window.$ = jquery;

window.addEventListener("DOMContentLoaded", function() {
    $('#fetch').click((e) => {
        $('nav a').removeClass('is-active');
        $(e.target).addClass('is-active');
        document.body.classList.add("loading");
    });
});
