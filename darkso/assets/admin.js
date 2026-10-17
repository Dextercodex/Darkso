(function ($) {
    var STORE = 'darkso_admin_tab';

    function preview(row) {
        var inputs = row.find('input.darkso-color');
        var from = inputs.eq(0).val() || '#ffffff';
        var to = inputs.eq(1).val() || '#121212';
        row.find('.darkso-chip--from').css({ background: from, color: to });
        row.find('.darkso-chip--to').css({ background: to, color: from });
    }

    function bindPickers(scope) {
        $(scope)
            .find('.darkso-color')
            .not('.wp-color-picker')
            .wpColorPicker({
                change: function (event, ui) {
                    var input = $(event.target);
                    input.val(ui.color.toString());
                    setTimeout(function () {
                        preview(input.closest('tr'));
                    }, 0);
                },
                clear: function (event) {
                    var input = $(event.target);
                    setTimeout(function () {
                        preview(input.closest('tr'));
                    }, 0);
                },
            });
    }

    function reindex() {
        $('#darkso-table tbody tr').each(function (i) {
            $(this).find('input.darkso-note').attr('name', 'darkso_color_maps[' + i + '][note]');
            $(this).find('input.darkso-color').eq(0).attr('name', 'darkso_color_maps[' + i + '][from]');
            $(this).find('input.darkso-color').eq(1).attr('name', 'darkso_color_maps[' + i + '][to]');
        });
    }

    function showTab(name) {
        var found = false;
        $('.darkso-panel').each(function () {
            var match = $(this).data('panel') === name;
            $(this).toggleClass('is-active', match);
            if (match) {
                found = true;
            }
        });
        if (!found) {
            return;
        }
        $('.darkso-tab').each(function () {
            $(this).toggleClass('is-active', $(this).data('tab') === name);
        });
        try {
            sessionStorage.setItem(STORE, name);
        } catch (e) {}
    }

    function copyText(text, btn) {
        function done() {
            var old = btn.text();
            btn.addClass('is-done').text('کپی شد ✓');
            setTimeout(function () {
                btn.removeClass('is-done').text(old);
            }, 1400);
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(done);
            return;
        }

        var tmp = $('<textarea>').val(text).css({ position: 'fixed', opacity: 0 }).appendTo(document.body);
        tmp[0].select();
        try {
            document.execCommand('copy');
            done();
        } catch (e) {}
        tmp.remove();
    }

    $(function () {
        bindPickers(document);

        $('#darkso-table tbody tr').each(function () {
            preview($(this));
        });

        $('#darkso-add').on('click', function () {
            var row = $(
                '<tr>' +
                    '<td data-label="توضیح"><input type="text" class="darkso-note regular-text" placeholder="مثلاً پس‌زمینه هدر"></td>' +
                    '<td data-label="از رنگ"><input type="text" class="darkso-color" value="#ffffff"></td>' +
                    '<td data-label="به رنگ"><input type="text" class="darkso-color" value="#121212"></td>' +
                    '<td data-label="پیش‌نمایش"><span class="darkso-preview">' +
                    '<span class="darkso-chip darkso-chip--from" style="background:#ffffff;color:#121212">Aa</span>' +
                    '<span class="darkso-arrow">←</span>' +
                    '<span class="darkso-chip darkso-chip--to" style="background:#121212;color:#ffffff">Aa</span>' +
                    '</span></td>' +
                    '<td><button type="button" class="button darkso-remove" aria-label="حذف ردیف">حذف</button></td>' +
                    '</tr>'
            );
            $('#darkso-table tbody').append(row);
            bindPickers(row);
            reindex();
            row.find('.darkso-note').trigger('focus');
        });

        $('#darkso-table').on('click', '.darkso-remove', function () {
            $(this).closest('tr').remove();
            reindex();
        });

        $('.darkso-tab').on('click', function () {
            showTab($(this).data('tab'));
        });

        $(document).on('click', '.darkso-copy', function (e) {
            e.preventDefault();
            copyText($(this).data('copy'), $(this));
        });

        var start = '';
        try {
            start = sessionStorage.getItem(STORE) || '';
        } catch (e) {}
        if (!start && window.location.hash) {
            start = window.location.hash.replace('#', '');
        }
        if (start) {
            showTab(start);
        }

        setTimeout(function () {
            $('.darkso-toast').fadeOut(400);
        }, 3500);
    });
})(jQuery);
