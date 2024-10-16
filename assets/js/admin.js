jQuery(document).ready(function($){
    // Initialisierung des Farbpickers
    $('.mm-color-field').wpColorPicker();

    // Hintergrundbild Upload
    var mediaUploader;
    $('#mm_upload_background_image_button').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Hintergrundbild auswählen',
            button: {
                text: 'Bild auswählen'
            },
            multiple: false
        });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#mm_background_image_id').val(attachment.id);
            $('#mm_background_image_preview').attr('src', attachment.url).show();
            $('#mm_remove_background_image_button').show();
        });
        mediaUploader.open();
    });

    $('#mm_remove_background_image_button').click(function(e){
        e.preventDefault();
        $('#mm_background_image_id').val('');
        $('#mm_background_image_preview').hide();
        $(this).hide();
    });

    // Logo Upload
    $('#mm_upload_logo_image_button').click(function(e) {
        e.preventDefault();
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }
        mediaUploader = wp.media.frames.file_frame = wp.media({
            title: 'Logo auswählen',
            button: {
                text: 'Logo auswählen'
            },
            multiple: false
        });
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#mm_logo_image_id').val(attachment.id);
            $('#mm_logo_image_preview').attr('src', attachment.url).show();
            $('#mm_remove_logo_image_button').show();
        });
        mediaUploader.open();
    });

    $('#mm_remove_logo_image_button').click(function(e){
        e.preventDefault();
        $('#mm_logo_image_id').val('');
        $('#mm_logo_image_preview').hide();
        $(this).hide();
    });
});