window.addEventListener("DOMContentLoaded", function() {
    $('.u_active').click((e) => {
        e.preventDefault();
        if (confirm($(e.target).text() === 'No' ? 'Update logs for this user?' : 'Stop updating logs for this user?')) {
            $('[name=action]').val('setActive');
            $('[name=target]').val($(e.target).parent().parent().data('user'));
            $('[name=value]').val($(e.target).text() === 'Yes' ? '0' : '1');
            $('#form').submit();
        } else {
            alert('Operation cancelled');
        }
    });
    $('.u_admin').click((e) => {
        e.preventDefault();
        if (confirm($(e.target).text() === 'No' ? 'Make this user an administrator?' : 'Revoke admin access for this user?')) {
            $('[name=action]').val('setAdmin');
            $('[name=target]').val($(e.target).parent().parent().data('user'));
            $('[name=value]').val($(e.target).text() === 'Yes' ? '0' : '1');
            $('#form').submit();
        } else {
            alert('Operation cancelled');
        }
    });
    $('.u_is_visible').click((e) => {
        e.preventDefault();
        if (confirm($(e.target).text() === 'No' ? 'Make this user visible?' : 'Make this user invisible?')) {
            $('[name=action]').val('setVisible');
            $('[name=target]').val($(e.target).parent().parent().data('user'));
            $('[name=value]').val($(e.target).text() === 'Yes' ? '0' : '1');
            $('#form').submit();
        } else {
            alert('Operation cancelled');
        }
    });
    $('.u_logs_purge').click((e) => {
        e.preventDefault();
        if (confirm('Purge all logs for this user?')) {
            $('[name=action]').val('purgeLogs');
            $('[name=target]').val($(e.target).parent().parent().data('user'));
            $('[name=value]').val('1');
            $('#form').submit();
        } else {
            alert('Operation cancelled');
        }
    });
    $( "#status" ).delay(2500).fadeOut( 500, function() {
        // Animation complete.
    })
})
