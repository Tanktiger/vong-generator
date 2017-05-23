/**
 * Created by Tom on 23.05.2017.
 */
$(document).on("ready", function () {
    var textClipboard = new Clipboard('.copy-vong-text-button');
    var picClipboard = new Clipboard('.copy-vong-picture-button');

    textClipboard.on('success', function(e) {
        var button = $(e.trigger);
        button.html('Kopiert <i class="fa fa-check"></i>');
        button.prop('disabled', true);

        setTimeout(function () {
            button.html('Text kopieren');
            button.prop('disabled', false);
        }, 3000);

        e.clearSelection();
    });

    textClipboard.on('error', function(e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    });

    picClipboard.on('success', function(e) {
        var button = $(e.trigger);
        button.html('Kopiert <i class="fa fa-check"></i>');
        button.prop('disabled', true);

        setTimeout(function () {
            button.html('Bild kopieren');
            button.prop('disabled', false);
        }, 3000);

        e.clearSelection();
    });

    picClipboard.on('error', function(e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    });

    //copy text from Textarea
    var copyTextareaBtn = $('#copyVongText');
    copyTextareaBtn.on('click', function(event) {
        var copyTextarea = $('#vongText');
        copyTextarea.select();

        try {
            var successful = document.execCommand('copy');
            if (successful) {
                copyTextareaBtn.html('Kopiert <i class="fa fa-check"></i>');
                copyTextareaBtn.prop('disabled', true);

                setTimeout(function () {
                    copyTextareaBtn.html('Text kopieren <i class="fa fa-files-o"></i>');
                    copyTextareaBtn.prop('disabled', false);
                }, 3000)
            }

        } catch (err) {
            console.log('Oops, unable to copy');
        }
    });

    //generate text
    $('#generateTextButton').on("click", function () {
        var button = $(this);
        console.log($('#normalText').val());
        generateText($('#normalText').val());

        button.prop('disabled', true);
        setTimeout(function () {
            button.prop('disabled', false);
        }, 2000);
    });

    var copyVongPicClipboard = new Clipboard('#copyVongPicture', {
        text: function(trigger) {
            return $(trigger).parent().find("img").attr("src");
        }
    });

    copyVongPicClipboard.on('success', function(e) {
        var button = $(e.trigger);
        button.html('Kopiert <i class="fa fa-check"></i>');
        button.prop('disabled', true);

        setTimeout(function () {
            button.html('Bild Url kopieren <i class="fa fa-image"></i>');
            button.prop('disabled', false);
        }, 10000);

        e.clearSelection();
    });

    copyVongPicClipboard.on('error', function(e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
    });

});

function likeText(id) {
    $.post("vong.php", {like: true, id: id})
        .done(function (response) {
            if (response && response.success) {
                $('#likeIcon'+id).removeAttr("onclick").addClass("font-yellow");
                $('#likeCount'+id).text(parseInt($('#likeCount'+id).text()) + 1);
            }

        });
}

function generateText(text) {
    $.post("vong.php", {text: text.replace(/(\r\n|\n|\r)/gm,"#nl#"), pic: true})
        .done(function (response) {
            if (response && response.vong) {
                $('#vongText').val(response.vong);
                $('#previewImage').parent().show();
                $('#previewImage').attr("src", response.url);
            }

        });
}

jQuery.fn.selectText = function(){
    var doc = document
        , element = this[0]
        , range, selection
        ;
    if (doc.body.createTextRange) {
        range = document.body.createTextRange();
        range.moveToElementText(element);
        range.select();
    } else if (window.getSelection) {
        selection = window.getSelection();
        range = document.createRange();
        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);
    }
};