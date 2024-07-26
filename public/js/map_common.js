function mapMarkerColSetActions() {
    $('#markerlist thead th.sort').on('click', function(){
        var i, initial, me, sortBy, sortOrder, sortType;
        me = $(this);
        i = me.attr('id').split('|');
        sortBy = i[0];
        sortOrder = i[1];
        sortType = me.data('type');
        if (sortBy === LMap.sortBy) {
            sortOrder = (LMap.sortOrder === 'a' ? 'd' : 'a');
            me.attr('id', sortBy + '|' + LMap.sortOrder);
        } else {
            me.attr('id', sortBy + '|a');
        }
        LMap.sortBy = sortBy;
        LMap.sortOrder = sortOrder;
        console.log('idx ' + sortBy + ' order ' + sortOrder + ' of type ' + sortType);
        mapMarkerColSort(sortBy, sortOrder, sortType);
    });
}

function mapMarkerColSort(idx, dir, type) {
    var cols =  $('#markerlist thead tr th');
    var col =   $('#markerlist thead tr th:eq(' + idx + ')')
    var tbody = $('#markerlist tbody');

    cols.removeClass('sorted');
    col.addClass('sorted');

    tbody.find('tr').sort(function (a, b) {
        var tda = $(a).find('td:eq(' + idx +')').data('val');
        var tdb = $(b).find('td:eq(' + idx +')').data('val');
        if (type === 'number') {
            tda = parseFloat(tda);
            tda = parseFloat(tda);
        }
        switch(dir) {
            case 'a':
                return (tda > tdb ? 1 : (tda < tdb ? -1 : 0));
            case 'd':
                return (tdb > tda ? 1 : (tdb < tda ? -1 : 0));
        }
    }).appendTo(tbody);
}
