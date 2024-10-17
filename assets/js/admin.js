jQuery(document).ready(function($){
    // Initialize Color Picker
    $('.mm-color-field').wpColorPicker();

    // Tab Navigation
    $('.mm-nav-tab').click(function(e){
        e.preventDefault();
        var tab = $(this).attr('href');

        $('.mm-nav-tab').removeClass('mm-nav-tab-active');
        $(this).addClass('mm-nav-tab-active');

        $('.mm-tab-content').removeClass('mm-active');
        $(tab).addClass('mm-active');
    });

    // Media Upload
    var mediaUploader;
    $('.mm-upload-button').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var target = $('#' + button.data('target'));
        var preview = $('#' + button.data('target') + '_preview');

        mediaUploader = wp.media({
            title: 'Bild auswählen',
            button: {
                text: 'Bild auswählen'
            },
            multiple: false
        });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            target.val(attachment.id);
            preview.attr('src', attachment.url).show();
            button.siblings('.mm-remove-button').show();
        });
        mediaUploader.open();
    });

    $('.mm-remove-button').click(function(e){
        e.preventDefault();
        var button = $(this);
        var target = $('#' + button.data('target'));
        var preview = $('#' + button.data('target') + '_preview');
        target.val('');
        preview.hide();
        $(this).hide();
    });
});