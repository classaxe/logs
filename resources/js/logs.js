let frm = {
    count: function() {
        let logs =      $('.list tbody tr:visible').length;
        let total =     $('.list tbody tr').length;
        $('#logsShown').html((logs === total ? 'All ' : '') + '<strong>' + logs + '</strong> log' + (logs ===1 ? '' : 's'));
    },
    reset: function() {
        $('body').addClass('loading');
        $(window).resize();
        window.setTimeout(function() { frm.reset_doit()}, 0);
    },
    reset_doit: function() {
        frm.count();
        $("body").removeClass("loading");
    },
    update: function() {
        $('body').addClass('loading');
        window.setTimeout(function() { frm.update_doit()}, 0);
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
    },
}


window.setVal = function(source, value) {
    $('input[name=' + source + ']').val(value);
    frm.update();
    return false;
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
        $('input[name=call]').val('');
        $('input[name=sp]').val('');
        $('input[name=itu]').val('');
        $('input[name=gsq]').val('');
        frm.update();
        $(this).blur();
    });
    $('td[data-link]').each(function() {
        var link = $(this).attr('data-link');
//        console.log(link);
        $(this).html("<a href=\"#\" onclick=\"return setVal('" + link + "\', \'" + $(this).text() +"\')\">" + $(this).html() + "</a>");
    })
});
