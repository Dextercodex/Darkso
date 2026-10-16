(function ($) {
    function bindPickers(scope) {
        $(scope)
            .find('.darkso-color')
            .not('.wp-color-picker')
            .wpColorPicker();
    }

    function reindex() {
        $('#darkso-table tbody tr').each(function (i) {
            $(this)
                .find('input.darkso-note')
                .attr('name', 'darkso_color_maps[' + i + '][note]');
            $(this)
                .find('input.darkso-color')
                .eq(0)
                .attr('name', 'darkso_color_maps[' + i + '][from]');
            $(this)
                .find('input.darkso-color')
                .eq(1)
                .attr('name', 'darkso_color_maps[' + i + '][to]');
        });
    }

    $(function () {
        bindPickers(document);

        $('#darkso-add').on('click', function () {
            var row = $(
                '<tr>' +
                    '<td><input type="text" class="darkso-note regular-text" placeholder="مثلاً پس‌زمینه هدر"></td>' +
                    '<td><input type="text" class="darkso-color" value="#ffffff"></td>' +
                    '<td><input type="text" class="darkso-color" value="#121212"></td>' +
                    '<td><button type="button" class="button darkso-remove">حذف</button></td>' +
                    '</tr>'
            );
            $('#darkso-table tbody').append(row);
            bindPickers(row);
            reindex();
        });

        $('#darkso-table').on('click', '.darkso-remove', function () {
            $(this).closest('tr').remove();
            reindex();
        });
    });
})(jQuery);
