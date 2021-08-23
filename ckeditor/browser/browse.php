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
    <script src="./js/globals.js"></script>
    <script src="./js/context.js"></script>
    <script src="./js/services.js"></script>
</head>

<?php
include_once '../uploader/functions.php';

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

            $out .=    "<button class='btn border-top-0 border-secondary rounded-0 shadow-none w-sidebar context-menu-target' data-path='{$directory['path']}' data-ctx-item-type='folder'>";
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
    <div class="container-fluid wrapper" style="background-color: #f7f8f9;">
        <div class="header row border-header" style="padding: 10px;">
            <div class="col-8">
                <button class="btn btn-light bg-white border border-secondary upload">
                    <i class="bi bi-upload"></i>
                    <span>Upload</span>
                </button>
                <!-- Hide input file -->
                <input type="file" name="uploadfile" id="uploadfile" style="display: none;" />
                <button class="btn btn-light bg-white border border-secondary subfolder">
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
                <button class="btn border-top-0 border-secondary rounded-0 shadow-none w-sidebar context-menu-target<?php echo trim($directory['path'], '/\\') == 'Images' ? ' folder-selected' : ''; ?>"
                        data-ctx-item-type="folder"
                        data-level="<?php echo $directory['level']; ?>"
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
                    echo subfolders($directory['children'], createId($directory['name']), $directory['level']);
                }
                ?><!-- /.collapse -->
            <?php endforeach; ?>
            </div><!-- /.sidebar -->

            <div class="col-9 main-content"></div><!-- /.main-content -->
        </div><!-- /.content -->
    </div>

    <!-- Modal -->
    <div class="modal fade modal-common" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header p-1">
                    <h5 class="modal-title" id="modalLabel">Preview Image</h5>
                    <button type="button" class="border-0 bg-transparent close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-5">
                                <img alt="Preview Image" height="200" width="200" />
                            </div>
                            <div class="col-7">
                                <div class="fileinfo" style="color: grey;">
                                    <div class="filename">
                                        <label >File Name:</label>
                                        <span></span>
                                    </div>
                                    <div class="filetype">
                                        <label style="color: grey;">Type:</label>
                                        <span></span>
                                    </div>
                                    <div class="filesize">
                                        <label style="color: grey;">Size:</label>
                                        <span></span>
                                    </div>
                                </div>
                                <!-- Show error message -->
                                <div class="uploaderror" style="display: none; color: red; padding-top: 40px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-1">
                    <div class="cancelok">
                        <button type="button" class="btn btn-primary btn-sm pt-0 pb-0 border-secondary rounded-0 ok modal-save">Save</button>
                        <button type="button" class="btn btn-secondary btn-sm pt-0 pb-0 border-secondary rounded-0 close" data-dismiss="modal">Cancel</button>
                    </div>
                    <!-- Show processing status -->
                    <div class="loader" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.Modal -->

    <!-- Context Menu -->
    <div id="context-menu-folder" class="context-menu"></div>
    <!-- /Context Menu -->

<script>
    function showImages(path) {
        $.ajax({
            url: '../uploader/do_file.php',
            method: 'POST',
            data: { path, action: 'read' },
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
                        out +=      `<div class="wrap-image context-menu-target" data-mime="${info.mime}" data-path="${info.folder}/${info.basename}" data-ctx-item-type="image">`;
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

                    // Restart context menu for binding images to it
                    bindContextMenu();
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log('error =>', jqXHR, textStatus, errorThrown);
            }
        });
    }

    function showSubfolderModal() {
        $('body').append('<div class="wrap-modal-subfolder" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 2000;"></div>');
        $('body').find('.wrap-modal-subfolder').load('./modals/create-subfolder.html');
    }

    function showRenameFolderModal() {
        $('body').append('<div class="wrap-modal-subfolder" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 2000;"></div>');
        $('body').find('.wrap-modal-subfolder').load('./modals/rename-folder.html');
    }

    function showDeleteFolderModal(elementTarget) {
        let folder = $(elementTarget).data('path');
        $('body').append(`<div class="wrap-modal-subfolder" data-folder="${folder}" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 2000;"></div>`);
        $('body').find('.wrap-modal-subfolder').load('./modals/delete-folder.html');
    }

    function showRenameFileModal(elementTarget) {
        let path = $(elementTarget).attr('data-path');
        $('body').append(`<div data-path="${path}" class="wrap-modal-subfolder" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 2000;"></div>`);
        $('body').find('.wrap-modal-subfolder').load('./modals/rename-file.html');
    }

    var configs = {
        selectorTarget: '.context-menu-target',
        selectorContext: '#context-menu-folder',
        types: {
            folder: [
                {
                    id: 'subfolder',
                    icon: '<i class="bi bi-folder-plus"></i>',
                    label: 'New Subfolder'
                },
                {
                    id: 'rename',
                    icon: '<i class="bi bi-file-earmark-ruled"></i>',
                    label: 'Rename',
                    disabled: function (elementTarget) {
                        return $(elementTarget).data('level') == 0;
                    }
                },
                {
                    id: 'delete',
                    icon: '<i class="bi bi-trash"></i>',
                    label: 'Delete',
                    disabled: function (elementTarget) {
                        return $(elementTarget).data('level') == 0;
                    }
                },
            ],
            image: [
                {
                    id: 'apply',
                    icon: '<i class="bi bi-check-lg"></i>',
                    label: 'Apply'
                },
                {
                    id: 'view',
                    icon: '<i class="bi bi-eye"></i>',
                    label: 'View'
                },
                {
                    id: 'download',
                    icon: '<i class="bi bi-download"></i>',
                    label: 'Download'
                },
                {
                    id: 'edit',
                    icon: '<i class="bi bi-pencil"></i>',
                    label: 'Edit'
                },
                {
                    id: 'rename',
                    icon: '<i class="bi bi-file-earmark-ruled"></i>',
                    label: 'Rename'
                },
                {
                    id: 'delete',
                    icon: '<i class="bi bi-trash"></i>',
                    label: 'Delete'
                },
            ]
        },
        activations: { // Automatically click target elment to be selected when click mouse right on it
            image: true,
            folder: true,
        },
        actions: {
            'folder.subfolder': function (elementTarget, event) {
                showSubfolderModal(elementTarget);
            },
            'folder.rename': function (elementTarget, event) {
                showRenameFolderModal(elementTarget);
            },
            'folder.delete': function (elementTarget, event) {
                showDeleteFolderModal(elementTarget);
            },
            'image.apply': function (elementTarget, event) {
                let path = $(elementTarget).data('path');
                let mime = $(elementTarget).data('mime');
                let src  = $(elementTarget).find('img').attr('src');
                // If it is an image, apply it to ckeditor
                if (/^image\/.*/gi.test(mime)) {
                    return returnFileUrl(src);
                } else {
                    alert('Only apply for image files!')
                }
            },
            'image.view': function (elementTarget, event) {
                Service.Image.view(function (success, data) {
                    
                });
            },
            'image.download': function (elementTarget, event) {
                let url = $(elementTarget).find('img').attr('src');
                Service.download(url);
            },
            'image.edit': function (elementTarget, event) {
                Service.Image.edit(function (success, data) {
                    
                });
            },
            'image.rename': function (elementTarget, event) {
                console.log('[rename] elementTarget => ', elementTarget);
                showRenameFileModal(elementTarget);
            },
            'image.delete': function (elementTarget, event) {
                Service.Image.delete(function (success, data) {
                    
                });
            }
        }
    };

    function bindContextMenu() {
        ContextMenu.start(configs);
    }

    $(function() {
        // Bind context menu
        bindContextMenu();

        // Reset input file after modal closed
        $('.modal').on('hide.bs.modal', function (e) {
            // Reset input file
            $('#uploadfile').val('');
            // Hide loader, error message if has
            $('.modal .cancelok').show();
            $('.modal .uploaderror').hide();
            $('.modal .loader').hide();
        });

        // Hide modal when click on close button
        $('.modal').on('click', '.close', function () {
            $('.modal').modal('hide');
        })

        // Upload file
        $(document).on('click', '.upload', function () {
            // Open dialog for choosing upload file
            $('#uploadfile').click();
        });

        // Subfolder (modal)
        $(document).on('click', '.subfolder', function () {
            // $('body').append('<div class="wrap-modal-subfolder" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 2000;"></div>');
            // $('body').find('.wrap-modal-subfolder').load('./modals/subfolder.html');
            showSubfolderModal();
        })

        // Click on each image
        $(document).on('click', '.images .wrap-image', function() {
            $(this).closest('.images').find('.wrap-image').removeClass('image-selected');
            $(this).addClass('image-selected');
        });

        $('#uploadfile').on('change', function() {
            var file = this.files[0];
            let filename = file.name;
            let extension = filename.split('.').pop().toLowerCase();
            let mime = file.type;
            let MAX_SIZE = 1; // MB
            let filesize = file.size / 1024 / 1024; // Bytes to MB

            if (['gif', 'jpg', 'jpeg', 'png'].indexOf(extension) == -1) {
                alert('Only support the image formats: jpg, jpeg, gif, png');
                return;
            }

            if (filesize > MAX_SIZE) {
                alert('Maximum image size is not over 5 MB.');
                return;
            }

            var fr = new FileReader();

            fr.onload = function() {
                // Show image on preview modal
                let imageElement = $('.modal').find('img');
                imageElement.attr('src', fr.result);
                imageElement.attr('data-filename', filename);
                imageElement.attr('data-mime', mime);

                // Update image information on preview modal
                let modalBodyElement = $('.modal .modal-body');
                modalBodyElement.find('.filename span').html(filename);
                modalBodyElement.find('.filetype span').html(mime);
                modalBodyElement.find('.filesize span').html(format_filesize(file.size));

                // Show image preview modal
                $('.modal').modal('toggle');
            }
            fr.readAsDataURL(file);
        });

        // Save upload file on server
        $('.modal-save').on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();

            $('.modal .cancelok').hide();
            $('.modal .loader').show();

            let folderSelected = '';
            let folderElementSelected = $('.sidebar').find('.folder-selected');

            if (folderElementSelected.hasClass('folder-selected')) {
                folderSelected = folderElementSelected.data('path');
            }

            let imageElement = $('.modal').find('img');
            let filename = imageElement.data('filename');
            let mime = imageElement.data('mime');
            let blob = base64ToBlob(imageElement.attr('src'));

            let dataForm = new FormData();
            dataForm.append('file', blob, filename);
            dataForm.append('folder', folderSelected);

            $.ajax({
                url: '../uploader/do_upload.php',
                method: 'POST',
                data: dataForm,
                dataType: 'json',
                contentType: false,
                cache: false,
                processData: false,
                success: function(response, status, jqXHR) {
                    console.log('response => ', response)
                    if (response && response.success) {
                        $('.modal').modal('hide');
                        // Reload images at folder selected
                        folderElementSelected.find('.folder').click();
                        // Reset form: will be automatically called after modal hidden
                    } else {
                        $('.modal .cancelok').show();
                        $('.modal .uploaderror').html(response.error);
                    }
                    $('.modal .loader').hide();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('error =>', jqXHR, textSatatus, errorThrown);
                    $('.modal .cancelok').show();
                    $('.modal .uploaderror').html(jqXHR.responseText);
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
