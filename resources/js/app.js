import './bootstrap';
import jquery from 'jquery';
window.$ = jquery;

window.addEventListener("DOMContentLoaded", function() {
    $('#fetch').click((e) => {
        if (confirm('Fetch your logs from QRZ.com right now?\nThis may take a while.')) {
            $('nav a').removeClass('is-active');
            $(e.target).addClass('is-active');
            document.body.classList.add("loading");
            return true;
        }
        alert('Operation cancelled');
        return false;
    });
});
