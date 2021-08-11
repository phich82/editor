<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management</title>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php
        $files = array_diff(scandir('../uploader/uploads'), ['.', '..']);
        $imagePath = 'http://'.$_SERVER['HTTP_HOST'].'/ckeditor/uploader/uploads';
    ?>
    <div class="wrap-upload">
        <h2>Upload File</h2>
        <div class="wrap-file">
            <p>
                <input type="file" name="file" id="file" />
            </p>
            <p class="wrap-preview" style="display: none;">
                <img alt="Preview Image" height="200" width="200" />
            </p>
            <p class="wrap-save" style="display: none;">
                <button class="save">Save</button>
            </p>
        </div>
    </div>
    <div class="wrap-image">
        <h2>Available Images</h2>
        <div class="images">
            <?php foreach ($files as $file) : ?>
            <img class="image" src="<?php echo $imagePath.'/'.$file; ?>" height="100" width="100" alt="<?php echo $file; ?>" />
            <?php endforeach; ?>
        </div>
    </div>

<script>
    // Helper function to get parameters from the query string.
    function getUrlParam( paramName ) {
        var reParam = new RegExp('(?:[\?&]|&)' + paramName + '=([^&]+)', 'i');
        var match = window.location.search.match(reParam);

        return (match && match.length > 1) ? match[1] : null;
    }

    // Simulate user action of selecting a file to be returned to CKEditor.
    function returnFileUrl(fileUrl) {
        window.opener.CKEDITOR.tools.callFunction(getUrlParam('CKEditorFuncNum'), fileUrl, function() {
            // Get the reference to a dialog window.
            var dialog = this.getDialog();
            // Check if this is the Image Properties dialog window.
            if (dialog.getName() == 'image' ) {
                // Get the reference to a text field that stores the "alt" attribute.
                var element = dialog.getContentElement('info', 'txtAlt');
                // Assign the new value.
                if (element) {
                    // Get image name and assign it to alt attribute of img tag
                    let filename = fileUrl.split('/').pop().split('.').shift();
                    element.setValue(filename);
                }
            }
            // Return "false" to stop further execution. In such case CKEditor will ignore the second argument ("fileUrl")
            // and the "onSelect" function assigned to the button that called the file manager (if defined).
            // return false;
        });
        window.close();
    }

    $(function() {
        $('.images').on('click', '.image', function() {
            returnFileUrl($(this).attr('src'));
        });

        $('#file').on('change', function() {
            var file = this.files[0];
            let filename = file.name;
            let extension = filename.split('.').pop().toLowerCase();

            if (['gif', 'jpp', 'jpeg', 'png'].indexOf(extension) == -1) {
                alert('Only support image formats: jpg, jpeg, gif, png');
                return;
            }

            var fr = new FileReader();

            fr.onload = function() {
                let preview = $('.wrap-preview');
                preview.find('img').attr('src', fr.result);
                preview.show();
                $('.wrap-save').show();
            }
            fr.readAsDataURL(file);
        });

        $('.save').on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();

            let file = $('#file').prop('files')[0];
            let filename = file.name;
            let extension = filename.split('.').pop().toLowerCase();

            if (['gif', 'jpp', 'jpeg', 'png'].indexOf(extension) == -1) {
                alert('Only support image formats: jpg, jpeg, gif, png');
                return;
            }

            let dataForm = new FormData();
            dataForm.append('file', file);

            $.ajax({
                url: '../uploader/do_upload.php',
                method: 'POST',
                data: dataForm,
                contentType: false,
                cache: false,
                processData: false,
                beforeSend:  function() {
                    // Show waiting for uploading
                    $('.wrap-file').hide();
                    $('.wrap-upload').append('<p class="loading" style="color: green; font-style: italic;">Uploading...</p>');
                },
                success: function(data, status, jqXHR) {
                    alert(data, status);
                    $('.wrap-upload').find('.loading').remove();
                    if (data) { //SUCCESS
                        // Reset form
                        $('.wrap-file').find('input[type=file]').val('');
                        $('.wrap-file').find('img').attr('src', '');
                        $('.wrap-file').find('.wrap-preview').hide();
                        $('.wrap-file').find('.wrap-save').hide();
                        $('.wrap-file').show();

                        let filename = data.split('/').pop().split('.').shift();
                        // Append new image to available images area
                        $('.images').append('<img class="image" src="'+data+'" height="100" width="100" alt="'+filename+'" />');
                    } else { // FAILED
                        $('.wrap-file').show();
                    }
                }
            });
        });
    });
</script>
</body>
</html>
