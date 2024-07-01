let frm = {
    count: function() {
        let logs = $('tbody tr:visible').length;
        $('#logsShown').html('<strong>' + logs + '</strong> log' + (logs ===1 ? '' : 's'));
    },
    update: function() {
        let bands = [], conf, modes = [];
        $('.band input:checked').each(function(){
            bands.push($(this).data('band'));
        });
        $('.bandsAll').prop('checked', (bands.length === $('.band input').length ? 'checked' : false));
        $('.mode input:checked').each(function(){
            modes.push($(this).data('mode'));
        });
        $('.modesAll').prop('checked', (modes.length === $('.mode input').length ? 'checked' : false));
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
                        break;
                    }
                }
            }
            if (show) {
                show = false;
                for (i=0; i<modes.length; i++) {
                    if ($(this).hasClass('m' + modes[i])) {
                        show = true;
                        break;
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
//        console.log({bands:bands, conf:conf, modes:modes});
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
    $('.mode input[type=checkbox]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('.modesAll').click(function() {
        $('.mode input[type=checkbox][data-mode]').prop('checked', $(this).prop('checked'));
        $('.mode input[type=checkbox][data-mode]').trigger('change');
    });
    $('input[name=conf]').change(function() {
        frm.update();
        $(this).blur();
    });
});
