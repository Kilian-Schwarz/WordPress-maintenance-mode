// assets/js/admin.js

jQuery(document).ready(function($){
    // Initialize Color Pickers
    $('.mm-color-field').wpColorPicker();
    $('.mm-font-color-field').wpColorPicker();

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
    $('.mm-upload-button').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var target = $('#' + button.data('target'));
        var preview = $('#' + button.data('target') + '_preview');

        var mediaUploader = wp.media({
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
            $('#mm-settings-form').trigger('change'); // Trigger change for preview update
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
        $('#mm-settings-form').trigger('change'); // Trigger change for preview update
    });

    // Add Link Button
    $('#mm-add-link-button').click(function(e){
        e.preventDefault();
        var linkField = $('<div class="mm-link-field">' +
            '<input type="url" name="mm_social_links[]" value="" class="regular-text" />' +
            '<button type="button" class="button mm-remove-link-button">Entfernen</button>' +
            '</div>');
        $('#mm-links-container').append(linkField);
    });

    // Remove Link Button
    $('#mm-links-container').on('click', '.mm-remove-link-button', function(e){
        e.preventDefault();
        $(this).parent('.mm-link-field').remove();
    });

    // Update Preview on Form Change
    $('#mm-settings-form').on('input change', 'input, select, textarea', function() {
        // Delay to prevent excessive reloads
        clearTimeout(window.mmPreviewTimeout);
        window.mmPreviewTimeout = setTimeout(function() {
            // Serialize form data
            var formData = $('#mm-settings-form').serialize();
            $.post(mmAjax.ajax_url + '?action=mm_preview', formData, function(response) {
                var iframe = document.getElementById('mm-preview-iframe');
                iframe.contentWindow.document.open();
                iframe.contentWindow.document.write(response);
                iframe.contentWindow.document.close();
            });

            // Update Favicon and Title
            var faviconId = $('#mm_favicon_image_id').val();
            if (faviconId) {
                wp.media.attachment(faviconId).fetch().then(function() {
                    $('#mm-preview-favicon').attr('src', this.get('url'));
                });
            } else {
                $('#mm-preview-favicon').attr('src', mmAjax.site_icon_url);
            }
            $('#mm-preview-title').text($('input[name="mm_text"]').val() + ' - Maintenance Mode');
        }, 500);
    });

    // Initialize Date-Time Pickers
    $('input[type="datetime-local"]').each(function(){
        var savedValue = $(this).attr('value');
        if (!savedValue) {
            var currentDateTime = new Date().toISOString().slice(0,16);
            $(this).val(currentDateTime);
        }
    });

    // Reset Statistics
    $('#mm-reset-stats-button').click(function(e){
        e.preventDefault();
        if (confirm('Möchtest du die Statistiken wirklich zurücksetzen?')) {
            $.post(mmAjax.ajax_url, { action: 'mm_reset_statistics' }, function(response){
                if (response.success) {
                    alert('Statistiken wurden zurückgesetzt.');
                    location.reload();
                } else {
                    alert('Fehler beim Zurücksetzen der Statistiken.');
                }
            });
        }
    });

    // Download Statistics
    $('#mm-download-stats-button').click(function(e){
        e.preventDefault();
        window.location.href = mmAjax.ajax_url + '?action=mm_download_statistics';
    });

    // Display Statistics Chart
    if (typeof Chart !== 'undefined' && $('#mm-stats-chart').length) {
        $.post(mmAjax.ajax_url, { action: 'mm_get_statistics' }, function(response){
            if (response.success) {
                var ctx = document.getElementById('mm-stats-chart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: response.data.dates,
                        datasets: [{
                            label: 'Besucher',
                            data: response.data.counts,
                            borderColor: '#6D727D',
                            backgroundColor: 'rgba(109, 114, 125, 0.5)',
                            fill: true
                        }]
                    },
                    options: {
                        scales: {
                            x: {
                                title: {
                                    display: true,
                                    text: 'Datum'
                                }
                            },
                            y: {
                                title: {
                                    display: true,
                                    text: 'Anzahl der Besucher'
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });
            } else {
                $('#mm-stats-chart').replaceWith('<p>Keine Daten verfügbar.</p>');
            }
        });
    }
});