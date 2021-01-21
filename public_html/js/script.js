$(function () {
    $('.shorter').on('submit', function (e) {
        e.preventDefault();

        var $overlay = $('.overlay');
        $overlay.show();
        var $this = $(this);
        var $shortLink = $('.short-link');
        var $shortLinkValue = $('.short-link__value');
        var $error = $('.error');

        $shortLink.hide();
        $shortLinkValue
            .attr('href', '')
            .text('');
        $error.hide();

        $.post(
            $this.attr('action'),
            'url=' + $this.find('input').val(),
            function (response) {
                if (typeof response.short_link !== 'undefined') {
                    $shortLinkValue
                        .attr('href', response.short_link)
                        .text(response.short_link);
                    $shortLink.show();
                    $this.trigger('reset');
                }
            }
        ).fail(function (response) {
            if (
                typeof response.responseJSON !== 'undefined'
                && typeof response.responseJSON.errors !== 'undefined'
            ) {
                $error.text('Ошибка: ' + response.responseJSON.errors[0]);
            } else {
                $error.text('Неизвестная ошибка');
            }
            $error.show();
        }).always(function () {
            $overlay.hide();
        });
    });
});