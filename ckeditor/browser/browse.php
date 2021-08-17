<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link href="./css/style.css" rel="stylesheet">
    <link href="./css/context-menu.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNCOe7tC1doHpGoWe/6oMVemdAVTMs2xqW4mwXrXsW0L84Iytr2wi5v2QjrP/xp" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js" integrity="sha384-cn7l7gDp0eyniUwwAZgrzD06kc/tftFf19TOAs2zVinnD/C7E91j9yyk5//jjpt/" crossorigin="anonymous"></script>
    <script src="./js/contextmenu.js"></script>
</head>
<?php
    function dd($value) {
        echo '<pre>'.json_encode($value, JSON_PRETTY_PRINT).'</pre>';
        exit;
    }

    function createId($str) {
        return preg_replace('#\s+#', '_', strtolower(trim($str)));
    }

    function scanDirectory($base_dir = '../uploader/storage', $level = 0) {
        $directories = [];
        foreach (scandir($base_dir) as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $dir = $base_dir.DIRECTORY_SEPARATOR.$file;
            if (is_dir($dir)) {
                $directories[] = [
                    'level'    => $level,
                    'name'     => $file,
                    'path'     => preg_replace('#^\.\.\/uploader\/storage#', '', $dir),
                    'children' => scanDirectory($dir, $level +1)
                ];
            }
        }
        return $directories;
    }

    // echo '<pre>'.json_encode(scanDirectory('../uploader/storage'), JSON_PRETTY_PRINT).'</pre>';

    function subfolders($directories, $collapseId = '', $levelPrev = 0) {
        $out = "";
        $levelCurrent = $levelPrev;
        foreach ($directories as $directory) {
        if ($levelCurrent != $directory['level']) {
            $levelCurrent = $directory['level'];
            $out .= "<div class='collapse' id='$collapseId'>";
        }
            $tab = $levelCurrent * 20;

            $out .=    "<button class='btn border-top-0 border-secondary rounded-0 shadow-none w-sidebar' data-path='{$directory['path']}'>";
            $out .=        "<div class='row'>";
            $out .=            "<div class='col text-start folder' style='padding-left: {$tab}px;'>";
            $out .=                "<i class='bi bi-folder icon-folder'></i>\n";
            $out .=                "<span>{$directory['name']}</span>";
            $out .=            "</div>";

        if (!empty($directory['children'])) {
            $out .=            "<div class='col-2'>";
            $out .=                "<i class='bi bi-caret-right-fill icon-collapse' data-bs-toggle='collapse' data-bs-target='#".createId($directory['name'])."'></i>";
            $out .=            "</div>";
            $out .=        "</div>";
            $out .=    "</button>";
            $out .= subfolders($directory['children'], createId($directory['name']), $directory['level']);
        } else {
            $out .=        "</div>";
            $out .=    "</button>";
        }
        }
        if (!empty($directories)) {
            $out     .= "</div>";
        }
        return $out;
    }
?>

<body>
    <div class="container-fluid" style="background-color: #f7f8f9;">
        <div class="header row border-header" style="padding: 10px;">
            <div class="col-8">
                <button class="btn btn-light bg-white border border-secondary">
                    <i class="bi bi-upload"></i>
                    <span>Upload</span>
                </button>
                <button class="btn btn-light bg-white border border-secondary">
                    <i class="bi bi-folder-plus"></i>
                    <span>New Subfolder</span>
                </button>
                <button class="btn btn-light bg-white border border-secondary">
                    <i class="bi bi-arrows-fullscreen"></i>
                    <span>Maximize</span>
                </button>
                <button class="btn btn-light bg-white border border-secondary">
                    <i class="bi bi-fullscreen-exit"></i>
                    <span>Minimize</span>
                </button>
            </div>
            <div class="col-4">
                <div class="row">
                    <input type="text" name="search" id="search" placeholder="Search..." class="form-control col" />
                    <button class="btn btn-light col-2">
                        <i class="bi bi-gear-fill"></i>
                    </button>
                </div>
            </div>
        </div><!-- /.header -->

        <div class="content row border-content">
            <div class="col-3 reset-pad-col sidebar">
            <?php foreach (scanDirectory() as $directory): ?>
                <button class="btn border-top-0 border-secondary rounded-0 shadow-none w-sidebar<?php echo trim($directory['path'], '/\\') == 'Images' ? ' folder-selected' : ''; ?>"
                        data-path="<?php echo $directory['path']; ?>">
                    <div class="row">
                        <div class="col text-start folder">
                            <i class="bi bi-folder icon-folder"></i>
                            <span><?php echo $directory['name']; ?></span>
                        </div>
                        <?php if (!empty($directory['children'])): ?>
                        <div class="col-2">
                            <i class="bi bi-caret-right-fill icon-collapse"
                               data-bs-toggle="collapse"
                               data-bs-target="#<?php echo createId($directory['name']); ?>"
                            ></i>
                        </div>
                        <?php endif; ?>
                    </div>
                </button><!-- /.btn -->
                <!-- collapse -->
                <?php
                if (!empty($directory['children'])) {
                    print subfolders($directory['children'], createId($directory['name']), $directory['level']);
                }
                ?><!-- /.collapse -->
            <?php endforeach; ?>
            </div><!-- /.sidebar -->

            <div class="col-9 main-content"></div><!-- /.main-content -->
        </div><!-- /.content -->
    </div>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Preview Iamge</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <img alt="Preview Image" height="200" width="200" />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div><!-- /.Modal -->

    <!-- Context Menu -->
    <nav id="context-menu" class="context-menu">
        <ul class="context-menu__items">
            <li class="context-menu__item">
                <a href="#" class="context-menu__link" data-action="Apply"><i class="bi bi-check-lg"></i> Apply</a>
            </li>
            <li class="context-menu__item">
                <a href="#" class="context-menu__link" data-action="View"><i class="bi bi-eye"></i> View</a>
            </li>
            <li class="context-menu__item">
                <a href="#" class="context-menu__link" data-action="Download"><i class="bi bi-download"></i> Download</a>
            </li>
            <li class="context-menu__item">
                <a href="#" class="context-menu__link" data-action="Edit"><i class="bi bi-pencil"></i> Edit</a>
            </li>
            <li class="context-menu__item">
                <a href="#" class="context-menu__link" data-action="Rename"><i class="bi bi-file-earmark-ruled"></i> Rename</a>
            </li>
            <li class="context-menu__item">
                <a href="#" class="context-menu__link" data-action="Delete"><i class="bi bi-trash"></i> Delete</a>
            </li>
        </ul>
    </nav><!-- /Context Menu -->

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

    <!-- <x?php
        $files = array_diff(scandir('../uploader/uploads'), ['.', '..']);
        $imagePath = 'http://'.$_SERVER['HTTP_HOST'].'/ckeditor/uploader/uploads';
    ?> -->
    <!-- <div class="wrap-image">
        <h2>Available Images</h2>
        <div class="images">
            <x?php foreach ($files as $file) : ?>
            <img class="image" src="<x?php echo $imagePath.'/'.$file; ?>" height="100" width="100" alt="<x?php echo $file; ?>" />
            <x?php endforeach; ?>
        </div>
    </div> -->

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

    function showImages(path) {
        $.ajax({
        url: '../uploader/get_files.php',
        method: 'POST',
        data: { path },
        dataType: 'json',
        success: function (response) {
            console.log("response =>", response);
            if (response && response.success) {
                let out = '<div class="images">';
                let count = 1;
                response.data.forEach(function (info, idx) {
                    if (count === 1) {
                        out += '<div class="row row-image">';
                    }
                    out += '<div class="col-3 block-image">';
                    out +=      `<div class="wrap-image task" data-mime="${info.mime}" data-path="${info.folder}/${info.basename}">`;
                    out +=          `<img class="image" src="${info.src}" height="100" width="100%" alt="${info.filename}" />`;
                    out +=          `<div class="fname">${info.basename}</div>`;
                    out +=          `<div class="fmodified">${info.modified}</div>`;
                    out +=          `<div class="fsize">${info.size}</div>`;
                    out +=      '</div>';
                    out += '</div>';
                    if (count === 4) {
                        count = 0;
                        out += '</div>';
                    }
                    count++;
                });
                out += '</div>';

                $('.main-content').html(out);
            }
        },
        error: function (jqXHR, textSatatus, errorThrown) {
            console.log('error =>', jqXHR, textSatatus, errorThrown);
        }
    });
    }

    $(function() {
        $('.modal').on('click', '.close', function () {
            $('.modal').modal('hide');
        })
        // Click on each image
        $(document).on('click', '.images .wrap-image', function() {
            //returnFileUrl($(this).attr('src'));
            $(this).closest('.images').find('.wrap-image').removeClass('image-selected');
            $(this).addClass('image-selected');
        });

        $('#file').on('change', function() {
            var file = this.files[0];
            let filename = file.name;
            let extension = filename.split('.').pop().toLowerCase();

            if (['gif', 'jpg', 'jpeg', 'png'].indexOf(extension) == -1) {
                alert('Only support image formats: jpg, jpeg, gif, png');
                return;
            }

            var fr = new FileReader();

            fr.onload = function() {
                $('.modal').find('img').attr('src', fr.result);
                $('.modal').modal('toggle');
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

        // $('.sidebar').on('click', 'button', function () {
        $('.sidebar').on('click', '.folder', function () {
            let buttonThis = $(this).closest('button');

            // Remove previous selected folder
            buttonThis.closest('.sidebar').find('button').removeClass('folder-selected');
            // Highlight current selected folder
            buttonThis.addClass('folder-selected');

            // Change another folder icons when selecting a folder
            buttonThis.closest('.sidebar').find('button').each(function (idx, button) {
                // If button (folder) has subfolders & in collapsed status, folder icon will be open
                if (!$(button).hasClass('collapsed')) {
                    $(button).find('.icon-folder').removeClass('bi-folder2-open').addClass('bi-folder');
                }
            });

            // Change icon of folder selected
            let hasSubfolder = !!buttonThis.find('.icon-collapse').data('bs-toggle');
            if (!hasSubfolder) {
                buttonThis.find('.icon-folder').removeClass('bi-folder').addClass('bi-folder2-open');
            }

            let path = buttonThis.data('path');
            if (path) {
                console.log('path => ', path)
                showImages(path);
            }


            // // Remove previous selected folder
            // $(this).closest('.sidebar').find('button').removeClass('folder-selected');
            // // Highlight current selected folder
            // $(this).addClass('folder-selected');

            // // Change another folder icons when selecting a folder
            // $(this).closest('.sidebar').find('button').each(function (idx, button) {
            //     // If button (folder) has subfolders & in collapsed status, folder icon will be open
            //     if (!$(button).hasClass('collapsed')) {
            //         $(button).find('.icon-folder').removeClass('bi-folder2-open').addClass('bi-folder');
            //     }
            // });

            // // Change icon of folder selected
            // let hasSubfolder = !!$(this).find('.icon-collapse').data('bs-toggle');
            // if (!hasSubfolder) {
            //     $(this).find('.icon-folder').removeClass('bi-folder').addClass('bi-folder2-open');
            // }
        });

        $('.sidebar').on('click', '.icon-collapse', function () {
            let btnCollapse = $(this);
            let button = btnCollapse.closest('button');
            let idTargetCollapse = btnCollapse.data('bs-target');
            let collapsed = button.hasClass('collapsed');
            let icon = button.find('.icon-folder');
            if (collapsed) {
                btnCollapse.removeClass('bi-caret-down-fill').addClass('bi-caret-right-fill');
                icon.removeClass('bi-folder2-open').addClass('bi-folder');
                button.removeClass('collapsed');
            } else {
                btnCollapse.removeClass('bi-caret-right-fill').addClass('bi-caret-down-fill');
                icon.removeClass('bi-folder').addClass('bi-folder2-open');
                button.addClass('collapsed');
            }
        });

        // Automatically loading images when on reload page
        if ($('.sidebar').find('.folder-selected').hasClass('folder-selected')) {
            $('.sidebar').find('.folder-selected .folder').click();
        }

        // window.opener.CKEDITOR.instances.editor.openDialog('mypluginDialog');
        // CKEDITOR.instances.editor.commands.insertMyPlugin.exec();
    });
</script>
</body>
</html>
