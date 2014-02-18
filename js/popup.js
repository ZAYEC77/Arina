$(document).ready(function () {
    $('#popup-btn').click(function () {
        var title = $(this).attr('rel');
        $.fancybox.showActivity();
        $.ajax({
            type: 'POST',
            cache: false,
            url: $(this).attr('href'),
            data: $('#popup').serializeArray(),
            success: function (data) {
                $.fancybox(data, {
                    'title': title,
                    'titlePosition': 'inside',
                    'titleFormat': function (title, currentArray, currentIndex, currentOpts) {
                        return '<div id="tip7-title"><span><a href="javascript:;" onclick="$.fancybox.close();">close</a></span>' + (title && title.length ? '<b>' + title + '</b>' : '') + '</div>';
                    },
                    'showCloseButton': false,
                    'autoDimensions': false,
                    'width': 900,
                    'height': 'auto',
                    'onComplete': function () {
                        $('#fancybox-inner').scrollTop(0);
                    }
                });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $.fancybox('<div class="error">' + XMLHttpRequest.responseText + '</div>');
            }
        });
        return false;
    });

    $(document).on('click', '#fancybox-inner .close-code, #subj_close', function () {
        alert('Form is closing');
        $.fancybox.close();
        return false;
    });
});