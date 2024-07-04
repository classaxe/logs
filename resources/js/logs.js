let frm = {
    start: null,
    count: function() {
        let logs =      $('.list tbody tr:visible').length;
        let total =     $('.list tbody tr').length;
        $('#logsShown').html((logs === total ? 'all ' : '') + '<strong>' + logs + '</strong> log' + (logs ===1 ? '' : 's'));
    },
    update: function() {
        frm.start = Date.now();
        $('body').addClass('loading');
        window.setTimeout(function() { frm.update_doit()}, 1);
    },
    update_doit: function() {
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
            let b, i, call, sp, itu, gsq;
            call =  $('input[name=call]').val();
            sp =    $('input[name=sp]').val();
            itu =   $('input[name=itu]').val().replace(' ','');
            gsq =   $('input[name=gsq]').val();
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
            if (show && call !== '') {
                show = false;
                if ($(this).hasClass('cs' + call)) {
                    show = true;
                }
            }
            if (show && sp !== '') {
                show = false;
                if ($(this).hasClass('s' + sp)) {
                    show = true;
                }
            }
            if (show && itu !== '') {
                show = false;
                if ($(this).hasClass('i' + itu)) {
                    show = true;
                }
            }
            if (show && gsq !== '') {
                show = false;
                if ($(this).hasClass('g' + gsq)) {
                    show = true;
                }
            }
            if (show) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
        frm.count();
        $("body").removeClass("loading");
        console.log('Updated in ' + (Date.now() - frm.start).toLocaleString() + ' seconds');
    },
}


window.setVal = function(source, value) {
    switch(source) {
        case 'band':
            $('input[name=' + source + ']').prop('checked', false);
            $('input[data-band=' + value + ']').prop('checked', true);
            break;
        case 'mode':
            $('input[name=' + source + ']').prop('checked', false);
            $('input[data-mode=' + value + ']').prop('checked', true);
            break;
        default:
            $('input[name=' + source + ']').val(value);
            break;
    }
    frm.update();
    return false;
}
window.addEventListener("DOMContentLoaded", function(){
    $('input[name=band]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('.bandsAll').click(function() {
        $('input[name=band]').prop('checked', $(this).prop('checked'));
        $('input[name=band]').trigger('change');
    });
    $('input[name=mode]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('.modesAll').click(function() {
        $('input[name=mode]').prop('checked', $(this).prop('checked'));
        $('input[name=mode]').trigger('change');
    });
    $('input[name=conf]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('input[name=call]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('input[name=sp]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('input[name=itu]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('input[name=gsq]').change(function() {
        frm.update();
        $(this).blur();
    });
    $('button#reset').click(function() {
        $('input[name=band]').prop('checked','checked');
        $('input[name=mode]').prop('checked','checked');
        $('input#conf_All').prop('checked','checked');
        $('input[name=call]').val('');
        $('input[name=sp]').val('');
        $('input[name=itu]').val('');
        $('input[name=gsq]').val('');
        frm.update();
        $(this).blur();
    });
    $('td[data-link]').each(function() {
        var link = $(this).attr('data-link');
        $(this).html("<a href=\"#\" onclick=\"return setVal('" + link + "\', \'" + $(this).text() +"\')\">" + $(this).html() + "</a>");
    })
});
