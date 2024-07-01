import './bootstrap';

import jquery from 'jquery';
window.$ = jquery;

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

var frm = {
    count: function() {
        let logs = $('tbody tr:visible').length;
        $('#logsShown').html('<strong>' + logs + '</strong> log' + (logs ===1 ? '' : 's'));
    },
    update: function() {
        let bands = [], conf;
        $('.band input:checked').each(function(){
            bands.push($(this).data('band'));
        });
        conf = $('input[name=conf]:checked').val();
        $('.list tbody tr').each(function() {
            let b, i;
            var show = false;
            if (
                (conf === '') || (conf === 'N' && $(this).hasClass('cN')) || (conf === 'Y' && $(this).hasClass('cY'))
            ){
                for (i=0; i<bands.length; i++) {
                    if ($(this).hasClass('b' + bands[i])) {
                        show = true;
                    }
                }
            }
            if (show) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        frm.count();
        console.log({bands:bands, conf:conf});
    }
}

window.addEventListener("DOMContentLoaded", function(){
    $('.band input[type=checkbox]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('.bandsAll').click(function() {
        $('.band input[type=checkbox][data-band]').prop('checked', $(this).prop('checked'));
        $('.band input[type=checkbox][data-band]').trigger('change');
    });

    $('input[name=conf]').change(function() {
        frm.update();
        $(this).blur();
    });
});
