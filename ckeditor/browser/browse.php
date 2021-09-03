<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link href="./css/style.css" rel="stylesheet">
    <link href="./css/context-menu.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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

            $out .=    "<button class='btn border-top-0 border-secondary rounded-0 shadow-none w-sidebar context-menu-target' data-path='{$directory['path']}' data-level='{$directory['level']}' data-ctx-item-type='folder'>";
            $out .=        "<div class='row'>";
            $out .=            "<div class='col text-start folder' style='padding-left: {$tab}px;'>";
            $out .=                "<i class='bi bi-folder icon-folder'></i>\n";
            $out .=                "<span>{$directory['name']}</span>";
            $out .=            "</div>";

        if (!empty($directory['children'])) {
            $out .=            "<div class='col-2 has-children'>";
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
                        <div class="col-2 has-children">
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

    function markCurrentFolderSelected(elementTarget, className) {
        className = className || 'current-folder-selected';
        // Remove class 'current-folder-selected' of all previous elements
        $(elementTarget)
            .closest('.sidebar')
            .find(`.${className}`)
            .removeClass(className);
        // Add class 'current-folder-selected' for current folder selected
        $(elementTarget).addClass(className);
        return className;
    }

    // TODO: Handle more after created subfolder
    function showCreateSubfolderModal(elementTarget) {
        // Mark the selected current folder
        var selectedCurrentFolderClass = markCurrentFolderSelected(elementTarget);
        // Selected folder
        var folderSelected = $(elementTarget).attr('data-path');
        // Create temporary DOM for loading modal
        createWrapper(function (classNameWrap) {
            $('body').find(`.${classNameWrap}`).load('./modals/newedit.html', function () {
                $(function () {
                    var modal = $(document).find('.modal-app');
                    // Change modal title, input label & placeholder
                    modal.find('.modal-title').html('New Name');
                    modal.find('.modal-body .label-input').html('Type the new folder name:');
                    modal.find('.modal-body .label-input').attr('placeholder', 'Your folder name');
                    // Focus to input
                    setTimeout(function() {
                        modal.find('#input').focus();
                    }, 1000);

                    modal.modal('toggle');

                    modal.on('click', '.close', function () {
                        modal.modal('hide');
                    });

                    modal.on('hide.bs.modal', function (e) {
                        $(`.${classNameWrap}`).remove();
                    });

                    modal.on('click', '.ok', function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        let subfolder = modal.find('input[name="input"]').val().trim();

                        if (!subfolder) {
                            alert('You must enter a folder name!');
                            return;
                        }

                        var charsNotAllowed = /[<>:"\/\\|?*\x00-\x1F]|^(?:aux|con|clock\$|nul|prn|com[1-9]|lpt[1-9])$/i;
                        if (charsNotAllowed.test(subfolder)) {
                            alert('Folder name contains invalid characters!');
                            return;
                        }

                        if (!folderSelected) {
                            alert('Please select the parent folder before creating subfolder!');
                            return;
                        }

                        modal.find('.cancelok').hide();
                        modal.find('.loader').show();

                        let data = {action: 'create', folder: folderSelected + '/' + subfolder};

                        $.ajax({
                            url: '../uploader/do_folder.php',
                            method: 'POST',
                            data: data,
                            dataType: 'json',
                            contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
                            cache: false,
                            success: function(response, status, jqXHR) {
                                if (response && response.success) {
                                    modal.modal('hide');
                                } else {
                                    modal.find('.cancelok').show();
                                    modal.find('.error_msg').html(response.error).show();

                                    hideAfter(modal.find('.error_msg'));
                                }
                                modal.find('.loader').hide();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                modal.find('.cancelok').show();
                                modal.find('.uploaderror').html(jqXHR.responseText);
                                modal.find('.loader').hide();

                                hideAfter(modal.find('.error_msg'));
                                modal.find('.loader').hide();
                            }
                        });
                    });
                });
            });
        });
    }

    function showRenameFolderModal(elementTarget) {
        // Mark the selected current folder
        var selectedCurrentFolderClass = markCurrentFolderSelected(elementTarget);
        // Selected folder
        var folderSelected = $(elementTarget).attr('data-path');
        // Create temporary DOM for loading modal
        createWrapper(function (classNameWrap) {
            $('body').find(`.${classNameWrap}`).load('./modals/newedit.html', function () {
                function loadDataOnInput(modal) {
                    let input = modal.find('#input');
                    input.val(getFileName(folderSelected));
                    // Focus after 1s
                    setTimeout(function() {
                        input.focus();
                    }, 1000);
                }
                $(function () {
                    var modal = $(document).find('.modal-app');
                    // Change modal title, input label & placeholder
                    modal.find('.modal-title').html('Rename');
                    modal.find('.modal-body .label-input').html('Type the new folder name:');
                    modal.find('.modal-body .label-input').attr('placeholder', 'Your folder name');
                    // Focus to input
                    loadDataOnInput(modal);

                    modal.modal('toggle');

                    modal.on('click', '.close', function () {
                        modal.modal('hide');
                    });

                    modal.on('hide.bs.modal', function (e) {
                        $(`.${classNameWrap}`).remove();
                    });

                    modal.on('click', '.ok', function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        let folder = modal.find('input[name="input"]').val().trim();

                        if (!folder) {
                            alert('You must enter a folder name!');
                            return;
                        }

                        var charsNotAllowed = /[<>:"\/\\|?*\x00-\x1F]|^(?:aux|con|clock\$|nul|prn|com[1-9]|lpt[1-9])$/i;
                        if (charsNotAllowed.test(folder)) {
                            alert('Folder name contains invalid characters!');
                            return;
                        }

                        if (!folderSelected) {
                            alert('Please select the folder before renaming!');
                            return;
                        }

                        modal.find('.cancelok').hide();
                        modal.find('.loader').show();

                        let data = {action: 'rename', old_folder: folderSelected, folder: folder};

                        $.ajax({
                            url: '../uploader/do_folder.php',
                            method: 'POST',
                            data: data,
                            dataType: 'json',
                            contentType: 'application/x-www-form-urlencoded;charset=UTF-8',
                            cache: false,
                            success: function(response, status, jqXHR) {
                                if (response && response.success) {
                                    // Update info of selected current folder
                                    let selectedCurrentFolderElement = $(`.${selectedCurrentFolderClass}`);
                                    selectedCurrentFolderElement.attr('data-path', response.data.path)
                                    selectedCurrentFolderElement.find('.folder span').html(response.data.name);
                                    selectedCurrentFolderElement.removeClass(selectedCurrentFolderClass);
                                    // Hide modal
                                    modal.modal('hide');
                                } else {
                                    modal.find('.cancelok').show();
                                    modal.find('.error_msg').html(response.error).show();

                                    hideAfter(modal.find('.error_msg'));
                                }
                                modal.find('.loader').hide();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                modal.find('.cancelok').show();
                                modal.find('.error_msg').html(jqXHR.responseText).show();
                                hideAfter(modal.find('.error_msg'));
                                modal.find('.loader').hide();
                            }
                        });
                    });
                });
            });
        });
    }

    // TODO: Handle more after folder deleted
    function showDeleteFolderModal(elementTarget) {
        // Mark the selected current folder
        var selectedCurrentFolderClass = markCurrentFolderSelected(elementTarget);
        var folderDeleted = $(elementTarget).data('path');
        // Create temporary DOM for loading modal
        createWrapper(function (classNameWrap) {
            $('body').find(`.${classNameWrap}`).load('./modals/confirmation.html', function () {
                $(function () {
                    var modal = $(document).find('.modal-app');
                    // Add confirmation before deleting
                    modal.find('#message').html(`
                        <p style="text-align: center;">Are you sure to delete folder <strong>${folderDeleted}</strong>?</p>
                    `);

                    modal.modal('toggle');

                    modal.on('click', '.close', function () {
                        modal.modal('hide');
                    });

                    modal.on('hide.bs.modal', function (e) {
                        $(`.${classNameWrap}`).remove();
                    });

                    modal.on('click', '.ok', function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        modal.find('.cancelok').hide();
                        modal.find('.loader').show();

                        let data = {action: 'delete', folder: folderDeleted};

                        $.ajax({
                            url: '../uploader/do_folder.php',
                            method: 'POST',
                            data: data,
                            dataType: 'json',
                            cache: false,
                            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                            success: function(response, status, jqXHR) {
                                if (response && response.success) {
                                    // TODO:
                                    // 1. Remove deleted folder from sidebar
                                    // 2. Check whether it has the parent folder?
                                    // 2.1 If has, then if parent folder has more 2 subfolders then keeping dropdown of it.
                                    // 2.2 Otherwise, remove dropdown from parent folder
                                    // 3. Clear all images shown corresponding to the slected folder for deleting
                                    // 4. Last, automatically click on parent folder
                                    modal.modal('hide');
                                } else {
                                    modal.find('.cancelok').show();
                                    modal.find('.error_msg').html(response.error).show();

                                    hideAfter(modal.find('.error_msg'));
                                }
                                modal.find('.loader').hide();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                modal.find('.cancelok').show();
                                modal.find('.error_msg').html(jqXHR.responseText).show();

                                hideAfter(modal.find('.error_msg'));
                                modal.find('.loader').hide();
                            }
                        });
                    });
                });
            });
        });
    }

    function showRenameFileModal(elementTarget) {
        let oldFile = $(elementTarget).attr('data-path');
        // Create temporary DOM for loading modal
        createWrapper(function (classNameWrap) {
            $('body').find(`.${classNameWrap}`).load('./modals/newedit.html', function () {
                function loadDataOnInput(modal) {
                    let input = modal.find('#input');
                    input.val(getFileName(oldFile));
                    // Focus after 1s
                    setTimeout(function() {
                        input.focus();
                    }, 1000);
                }
                $(function () {
                    var modal = $(document).find('.modal-app');

                    // Change title, input label & placeholder
                    modal.find('.modal-title').html('Rename');
                    modal.find('.modal-body .label-input').html('Type the new file name:');
                    modal.find('.modal-body #input').attr('placeholder', 'Enter your filename');

                    loadDataOnInput(modal);

                    modal.modal('toggle');

                    modal.on('click', '.close', function (e) {
                        modal.modal('hide');
                    });

                    modal.on('hide.bs.modal', function (e) {
                        $(`.${classNameWrap}`).remove();
                    });

                    modal.on('click', '.ok', function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        let newFile = modal.find('input[name="input"]').val().trim();

                        // Validate: filename is empty
                        if (!newFile) {
                            alert('You must enter a filename!');
                            return;
                        }
                        // Validate: filename contains invalid characters
                        var charsNotAllowed = /[<>:"\/\\|?*\x00-\x1F]|^(?:aux|con|clock\$|nul|prn|com[1-9]|lpt[1-9])$/i;
                        if (charsNotAllowed.test(newFile)) {
                            alert('Filename contains invalid characters!');
                            return;
                        }

                        modal.find('.cancelok').hide();
                        modal.find('.loader').show();

                        let data = {
                            action: 'rename',
                            old_file: oldFile,
                            new_file: newFile
                        };

                        $.ajax({
                            url: '../uploader/do_file.php',
                            method: 'POST',
                            data: data,
                            cache: false,
                            dataType: 'json',
                            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                            success: function(response, status, jqXHR) {
                                console.log('response => ', response)
                                if (response && response.success) {
                                    let data = response.data;
                                    let imageSelected = $('.images').find('.image-selected');
                                    // Update information of image selected
                                    imageSelected.attr('data-path', data.path);
                                    imageSelected.attr('data-mime', data.mime);
                                    imageSelected.find('img').attr('src', data.src);
                                    imageSelected.find('img').attr('alt', data.filename);
                                    imageSelected.find('.fname').html(data.basename);
                                    imageSelected.find('.fmodified').html(data.modified);
                                    imageSelected.find('.fsize').html(data.size);
                                    // Hide modal
                                    modal.modal('hide');
                                } else {
                                    modal.find('.cancelok').show();
                                    modal.find('.error_msg').html(response.error).show();

                                    hideAfter(modal.find('.error_msg'));
                                }
                                modal.find('.loader').hide();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                modal.find('.cancelok').show();
                                modal.find('.error_msg').html(jqXHR.responseText).show();

                                hideAfter(modal.find('.error_msg'));
                                modal.find('.loader').hide();
                            }
                        });
                    });
                });
            });
        });
    }

    // TODO: Handle more when folder has subfolders
    function showDeleteFileModal(elementTarget) {
        let path = $(elementTarget).attr('data-path');
        // Add temporary DOM for loading modal
        createWrapper(function (classNameWrap) {
            // Load confirmation template
            $('body').find(`.${classNameWrap}`).load('./modals/confirmation.html', function () {
                // Handle next after template loaded
                $(function () {
                    var modal = $(document).find('.modal-app');
                    // Add confirmation before deleting
                    modal.find('#message').html(`
                        <p style="text-align: center;">Are you sure to delete file <strong>${path}</strong>?</p>
                    `);
                    // Change Text for Save button
                    modal.find('.ok').html('OK');
                    // Show modal
                    modal.modal('toggle');
                    // Hide modal when click on close button
                    $(document).on('click', '.modal-app .close', function () {
                        modal.modal('hide');
                    });
                    // Delete DOM
                    modal.on('hide.bs.modal', function (e) {
                        $(`.${classNameWrap}`).remove();
                    });
                    // Click on OK button
                    modal.on('click', '.ok', function (e) {
                        e.stopPropagation();
                        e.preventDefault();

                        modal.find('.cancelok').hide();
                        modal.find('.loader').show();

                        let data = {action: 'delete', file: path};

                        $.ajax({
                            url: '../uploader/do_file.php',
                            method: 'POST',
                            data: data,
                            cache: false,
                            dataType: 'json',
                            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',

                            // data: JSON.stringify(data), // server => parse body (json string) to array
                            // contentType: 'application/json;charset=utf-8',

                            // data: new FormData(),
                            // contentType: false, // data = new FormData()
                            // processData: false,
                            success: function(response, status, jqXHR) {
                                if (response && response.success) {
                                    // Reload image area
                                    let selectedCurrentFolder = $('.folder-selected').attr('data-path');
                                    showImages(selectedCurrentFolder);
                                    // Hide modal
                                    modal.modal('hide');
                                } else {
                                    modal.find('.cancelok').show();
                                    modal.find('.error_msg').html(response.error).show();
                                    hideAfter(modal.find('.error_msg'));
                                }
                                modal.find('.loader').hide();
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                modal.find('.cancelok').show();
                                modal.find('.error_msg').html(jqXHR.responseText).show();
                                hideAfter(modal.find('.error_msg'));
                                modal.find('.loader').hide();
                            }
                        });
                    });
                });
            });
        });
    }

    function showImageViewModal(elementTarget) {
        var selectedCurrentFolder = $('.sidebar .folder-selected').attr('data-path');
        var srcImgSelected = $(elementTarget).find('img').attr('src');

        load('./modals/slideshow.html', function (classNameWrap) {
            $(function () {
                var modal = $(document).find('.modal-app');
                var slideIndex = 1;

                modal.modal('toggle');

                modal.on('click', '.close', function (e) {
                    modal.modal('hide');
                });

                // Click PREV button
                modal.on('click', '.prev', function (e) {
                    goToSlide(-1);
                });

                // Click NEXT button
                modal.on('click', '.next', function (e) {
                    goToSlide(1);
                });

                modal.on('hide.bs.modal', function (e) {
                    $(`.${classNameWrap}`).remove();
                });

                // Show the image slides after modal shown
                modal.on('shown.bs.modal', function (e) {
                    // Get images by folder
                    $.ajax({
                        url: '../uploader/do_file.php',
                        method: 'POST',
                        data: { path: selectedCurrentFolder, action: 'read' },
                        dataType: 'json',
                        success: function (response) {
                            if (response && response.success) {
                                let out = '';
                                response.data.forEach(function (info, idx) {
                                    // Image selected will be shown
                                    if (info.src == srcImgSelected) {
                                        slideIndex = idx + 1;
                                    }
                                    out += `<div class="slide${ info.src != srcImgSelected ? ' faded' : ''}">
                                                <img src="${info.src}">
                                            </div>`;
                                });
                                modal.find('.slides').html(out);
                                // Show image selected
                                showSlides(slideIndex);
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert(jqXHR.responseText);
                            modal.modal('hide');
                        }
                    });
                });

                // Next/previous controls
                function goToSlide(n) {
                    showSlides(slideIndex += n);
                }
                // Show current slide
                function showSlides(n) {
                    var slides = modal.find('.slide');
                    if (n > slides.length) {slideIndex = 1}
                    if (n < 1) {slideIndex = slides.length}
                    // Hide all previous slides
                    for (let i = 0; i < slides.length; i++) {
                        slides[i].style.display = 'none';
                    }
                    // Show current slide
                    slides[slideIndex - 1].style.display = 'block';
                }
            });
        });
    }

    function showImageProcessorModal(elementTarget) {
        var pathImageSelected = $(elementTarget).attr('data-path');
        var srcImageSelected = $(elementTarget).find('img').attr('src');

        load('./modals/image-processor.html', function (classNameWrap) {
            $(function () {
                var modal = $(document).find('.modal-app');
                var MIN_VALUE_ADJUST = -100;
                var MAX_VALUE_ADJUST = 100;
                var DEF_VALUE_ADJUST = 0;
                var SLIDER_BARS_ADJUST = [
                    '#slider-brightness',
                    '#slider-contrast',
                    '#slider-saturation',
                    '#slider-exposure',
                    '#slider-sepia',
                    '#slider-sharpen',
                ];
                var CANVAS_DEFAULT_HEIGHT = 400;
                var CANVAS_DEFAULT_WIDTH  = 500;

                var canvas, ctx;
                var isLoadedImage = false;
                var degrees = 0;
                var curImgHeight = 0;
                var curImgWidth  = 0;
                var oriImgHeight = 0;
                var oriImgWidth  = 0;
                var img = new Image();
                img.crossOrigin = "Anonymous";

                // Crop image
                var cropBox = modal.find('.crop-box');
                var event_state = {};
                var constraint  = isKeepRatio('input[name="crop-keep-ratio"]');
                var crop_box_min_width  = 60;  // Change as required
                var crop_box_min_height = 60;
                var crop_box_max_width  = 800; // Change as required
                var crop_box_max_height = 500;

                // Set image name
                modal.find('.modal-header .image-name').text(getFileName(pathImageSelected));

                modal.modal('toggle');

                modal.on('click', '.close', function (e) {
                    modal.modal('hide');
                });

                modal.on('hide.bs.modal', function (e) {
                    $(`.${classNameWrap}`).remove();
                });

                // Handle image after modal shown
                modal.on('shown.bs.modal', function (e) {
                    // Show silder bars (adjust tool)
                    SLIDER_BARS_ADJUST.forEach(function (identity) {
                        $(identity).slider({
                            orientation: "horizontal", // vertical
                            animate: true,
                            range: 'min',
                            min: MIN_VALUE_ADJUST,
                            max: MAX_VALUE_ADJUST,
                            value: DEF_VALUE_ADJUST,
                            step: 1,
                            slide: debounce(function (event, ui) {
                                $(this).find('.ui-slider-handle').text(ui.value);
                                var curValue = parseInt(ui.value);
                                var imgX = 0;
                                var imgY = 0;
                                ctx.drawImage(img, imgX, imgY, canvas.width, canvas.height);
                                var scannedImg  = ctx.getImageData(imgX, imgY, canvas.width, canvas.height);
                                var scannedData = scannedImg.data; // raw pixel data in array

                                console.log('curValue => ', curValue)

                                switch ($(this).attr('id')) {
                                    case 'slider-brightness':
                                        // var imgX = 0;
                                        // var imgY = 0;
                                        // ctx.drawImage(img, imgX, imgY, canvas.width, canvas.height);
                                        // var scannedImg  = ctx.getImageData(imgX, imgY, canvas.width, canvas.height);
                                        // var scannedData = scannedImg.data; // raw pixel data in array
                                        var brightnessMul = 1 + curValue / 100; // brightness multiplier

                                        for (var i = 0; i < scannedData.length; i += 4) {
                                            var r = scannedData[i];     // Extract original red color [0 to 255]
                                            var g = scannedData[i + 1]; // Extract green
                                            var b = scannedData[i + 2]; // Extract blue

                                            brightenedR = brightnessMul * r;
                                            brightenedG = brightnessMul * g;
                                            brightenedB = brightnessMul * b;

                                            /**
                                             *
                                             * Remember, you should make sure the values brightenedRed,
                                             * brightenedGreen, and brightenedBlue are between
                                             * 0 and 255. You can do this by using
                                             * Math.max(0, Math.min(255, brightenedRed))
                                             *
                                             */
                                            scannedData[i]   = Math.max(0, Math.min(255, brightenedR));
                                            scannedData[i+1] = Math.max(0, Math.min(255, brightenedG));
                                            scannedData[i+2] = Math.max(0, Math.min(255, brightenedB));
                                        }
                                        scannedImg.data = scannedData;
                                        ctx.putImageData(scannedImg, imgX, imgY);
                                        break;
                                    case 'slider-contrast':
                                        var contrast = curValue * 2.55;
                                        if (contrast > 0) {
                                            for (var i = 0; i < scannedData.length; i += 4) {
                                                scannedData[i]     += (255 - scannedData[i])     * contrast / 255;            // red
                                                scannedData[i + 1] += (255 - scannedData[i + 1]) * contrast / 255;    // green
                                                scannedData[i + 2] += (255 - scannedData[i + 2]) * contrast / 255;    // blue
                                            }
                                        } else if (contrast < 0) {
                                            for (var i = 0; i < scannedData.length; i += 4) {
                                                scannedData[i]     += scannedData[i]     * (contrast) / 255;                  // red
                                                scannedData[i + 1] += scannedData[i + 1] * (contrast) / 255;          // green
                                                scannedData[i + 2] += scannedData[i + 2] * (contrast) / 255;          // blue
                                            }
                                        }
                                        scannedImg.data = scannedData;
                                        ctx.putImageData(scannedImg, imgX, imgY);
                                        break;
                                }
                            }),
                            create: function (event, ui) {
                                var v = $(this).slider('value');
                                console.log('create => ', ui.value, v)
                                $(this).find('.ui-slider-handle').text(v);
                            }
                        });
                    });

                    // Get canvas
                    canvas = document.getElementById('canvas');
                    ctx = canvas.getContext("2d");
                    // Waiting image until it is loaded
                    img.onload = function () {
                       isLoadedImage = true;
                        // Update image width & height in resize board
                        modal.find('input[name="resize-w"]').val(img.width);
                        modal.find('input[name="resize-h"]').val(img.height);
                        // Verify image size
                        oriImgHeight  = img.height;
                        oriImgWidth   = img.width;
                        curImgWidth   = oriImgWidth  > CANVAS_DEFAULT_WIDTH  ? CANVAS_DEFAULT_WIDTH  : oriImgWidth;
                        curImgHeight  = oriImgHeight > CANVAS_DEFAULT_HEIGHT ? CANVAS_DEFAULT_HEIGHT : oriImgHeight;
                        canvas.width  = curImgWidth;
                        canvas.height = curImgHeight;
                        // Show image
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    };
                    img.src = srcImageSelected;
                });

                // Resize image
                modal.on('click', '.apply-resize', function (e) {
                    // Current image size
                    curImgWidth  = parseInt(modal.find('input[name="resize-w"]').val());
                    curImgHeight = parseInt(modal.find('input[name="resize-h"]').val());
                    // Validate image sizes
                    if (isNaN(curImgWidth)) {
                        alert('Image width must be a positive number!');
                        return;
                    }
                    if (isNaN(curImgHeight)) {
                        alert('Image height must be a positive number!');
                        return;
                    }
                    if (curImgWidth <= 0) {
                        alert('Image width can not a negative number!');
                        return;
                    }
                    if (curImgHeight <= 0) {
                        alert('Image height can not a negative number!');
                        return;
                    }
                    if (curImgWidth > oriImgWidth) {
                        alert('You enter a width value less than ' + oriImgWidth + '!');
                        return;
                    }
                    if (curImgHeight > oriImgHeight) {
                        alert('You enter a height value less than ' + oriImgHeight + '!');
                        return;
                    }
                    // Resize image
                    if (isLoadedImage) {
                        canvas.width  = curImgWidth;
                        canvas.height = curImgHeight;
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        ctx.save();
                    }
                });
                // When resizing width of image
                modal.on('change', 'input[name="resize-w"]', function (e) {
                    if (isKeepRatio('input[name="resize-keep-ratio"]')) {
                        modal.find('input[name="resize-h"]').val(Number($(this).val()) * ratioImage());
                    }
                });
                // When resizing height of image
                modal.on('change', 'input[name="resize-h"]', function (e) {
                    if (isKeepRatio('input[name="resize-keep-ratio"]')) {
                        modal.find('input[name="resize-w"]').val(Number($(this).val()) * ratioImage());
                    }
                });

                // Rotating image by clockwise
                modal.on('click', '.apply-clockwise', function(e) {
                    degrees += 90 % 360;
                    rotateImage(degrees);
                    // Prevent image cliped when rotating over 4 times
                    if (degrees == 360) {
                        degrees = 0;
                    }
                });

                // Rotating image by counterclockwise
                modal.on('click', '.apply-counterclockwise', function(e) {
                    if (degrees == 0) {
                        degrees = 270;
                    } else {
                        degrees -= 90;
                    }
                    rotateImage(degrees);
                });

                modal.on('click', '.apply-crop', function (e) {
                    var left   = cropBox.offset().left - $(canvas).offset().left;
                    var top    = cropBox.offset().top - $(canvas).offset().top;
                    var width  = cropBox.width();
                    var height = cropBox.height();

                    canvas.width = width;
                    canvas.height = height;

                    ctx.drawImage(img, left, top, width, height, 0, 0, width, height);
                });

                // Keep aspect ratio of image
                modal.find('input[name="crop-keep-ratio"]').on('change', function () {
                    constraint  = isKeepRatio('input[name="crop-keep-ratio"]');
                });

                // Drag crop box
                cropBox.draggable({
                    scroll: true,
                    axis: "xy",
                    containment: '#image-canvas',
                    revert: false,
                    disable: false,
                    start: function(event, ui) { // when starting drag
                        //
                    },
                    drag: function(event, ui) { // when dragging
                        //
                    },
                    stop:function(event, ui) { // Dragging done
                        // Track positions of dragging
                    }
                });

                // Save state of event that it starts pulling corners of crop box
                function saveEventState(e) {
                    // Save the initial event details and container state
                    event_state.container_width  = cropBox.width();
                    event_state.container_height = cropBox.height();
                    event_state.container_left   = cropBox.offset().left;
                    event_state.container_top    = cropBox.offset().top;
                    event_state.mouse_x = (e.clientX || e.pageX || e.originalEvent.touches[0].clientX) + $(window).scrollLeft();
                    event_state.mouse_y = (e.clientY || e.pageY || e.originalEvent.touches[0].clientY) + $(window).scrollTop();

                    // This is a fix for mobile safari
                    // For some reason it does not allow a direct copy of the touches property
                    if (typeof e.originalEvent.touches !== 'undefined') {
                        event_state.touches = [];
                        $.each(e.originalEvent.touches, function(i, o) {
                            event_state.touches[i] = {};
                            event_state.touches[i].clientX = 0 + o.clientX;
                            event_state.touches[i].clientY = 0 + o.clientY;
                        });
                    }
                    event_state.event = e;
                }

                // Change size of crop box while pulling its corners
                function resizing(e) {
                    e.preventDefault();

                    // Turn off draggling crop box when pulling its corners
                    cropBox.draggable({disable: true});

                    var mouse={},width,height,left,top,offset=cropBox.offset(),$currentCropHandle=$(event_state.event.target);
                    mouse.x = (e.clientX || e.pageX || e.originalEvent.touches[0].clientX) + $(window).scrollLeft();
                    mouse.y = (e.clientY || e.pageY || e.originalEvent.touches[0].clientY) + $(window).scrollTop();

                    // TODO: check if width or height of crop box after resized over width or height of container,
                    // TODO: set width or height of crop box as width or height of container

                    // Position that crop box differently depending on the corner dragged and constraints
                    if ($currentCropHandle.hasClass('crop-point-bottom-right')) { // se
                        width  = mouse.x - event_state.container_left;
                        height = mouse.y - event_state.container_top;
                        left   = event_state.container_left;
                        top    = event_state.container_top;
                    } else if ($currentCropHandle.hasClass('crop-point-bottom-left')) { // sw
                        width  = event_state.container_width - (mouse.x - event_state.container_left);
                        height = mouse.y  - event_state.container_top;
                        left   = mouse.x;
                        top    = event_state.container_top;
                    } else if ($currentCropHandle.hasClass('crop-point-top-left')) { // nw
                        width  = event_state.container_width - (mouse.x - event_state.container_left);
                        height = event_state.container_height - (mouse.y - event_state.container_top);
                        left   = mouse.x;
                        top    = mouse.y;
                        // When press and hold SHIFT
                        if (constraint || e.shiftKey) {
                            top = mouse.y - ((width / event_state.container_width * event_state.container_height) - height);
                        }
                    } else if ($currentCropHandle.hasClass('crop-point-top-right')) { // ne
                        width  = mouse.x - event_state.container_left;
                        height = event_state.container_height - (mouse.y - event_state.container_top);
                        left   = event_state.container_left;
                        top    = mouse.y;
                        // When press and hold SHIFT
                        if (constraint || e.shiftKey){
                            top = mouse.y - ((width / event_state.container_width * event_state.container_height) - height);
                        }
                    } else if ($currentCropHandle.hasClass('crop-point-top')) { // n
                        width  = event_state.container_width;
                        height = event_state.container_height - (mouse.y - event_state.container_top);
                        left   = event_state.container_left;
                        top    = mouse.y;
                    } else if ($currentCropHandle.hasClass('crop-point-bottom')) { // s
                        width  = event_state.container_width;
                        height = mouse.y - event_state.container_top;
                        left   = event_state.container_left;
                        top    = event_state.container_top;
                    } else if ($currentCropHandle.hasClass('crop-point-left')) { // w
                        width  = event_state.container_width - (mouse.x - event_state.container_left);
                        height = event_state.container_height;
                        left   = mouse.x;
                        top    = event_state.container_top;
                    } else if ($currentCropHandle.hasClass('crop-point-right')) { // e
                        width  = mouse.x - event_state.container_left;
                        height = event_state.container_height;
                        left   = event_state.container_left;
                        top    = event_state.container_top;
                    }

                    // Optionally maintain aspect ratio (press and hold SHIFT)
                    if (constraint || e.shiftKey) {
                        height = width / event_state.container_width * event_state.container_height;
                    }

                    // Resize crop box
                    cropBox.css({width, height, transform: 'translate(0)'});
                    // Without this, Firefox will not re-calculate the the image dimensions until drag end
                    cropBox.offset({'left': left, 'top': top});
                }

                // When finished pulling corners of crop box
                function endResize(e) {
                    e.preventDefault();
                    // Turn off pulling corners of crop box
                    modal.off('mouseup touchend', endResize);
                    modal.off('mousemove touchmove', resizing);
                    // Turn on draggling crop box when finished pulling its corners
                    modal.find('.crop-box').draggable({disable: false});
                }

                // Start event for corners (8) of crop box
                cropBox.on('mousedown touchstart', '.crop-handle', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    saveEventState(e);

                    // When press and holder a corner of crop box via mouse
                    modal.on('mousemove touchmove', resizing);
                    // When release the mouse
                    modal.on('mouseup touchend', endResize);
                });

                function rotateImage(deg) {
                    if (deg == 90 || deg == 270) {
                        canvas.width  = curImgHeight;
                        canvas.height = curImgWidth;
                    } else {
                        canvas.width  = curImgWidth;
                        canvas.height = curImgHeight;
                    }

                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.save();

                    ctx.translate(canvas.width / 2, canvas.height / 2);
                    ctx.rotate(deg * Math.PI / 180);

                    if (deg == 90 || deg == 270) {
                        ctx.drawImage(img, -canvas.height / 2, -canvas.width / 2, canvas.height, canvas.width);
                    } else {
                        ctx.drawImage(img, -canvas.width / 2, -canvas.height / 2, canvas.width, canvas.height);
                    }

                    ctx.rotate(-deg * Math.PI / 180);
                    ctx.translate(-canvas.width / 2, -canvas.height / 2);
                    ctx.save();
                    // ctx.restore();
                }

                function isKeepRatio(identity) {
                    return modal.find(identity).is(':checked');
                }

                function ratioImage() {
                    return oriImgHeight / oriImgWidth;
                }

            });
        });
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
                showCreateSubfolderModal(elementTarget);
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
                showImageViewModal(elementTarget);
            },
            'image.download': function (elementTarget, event) {
                let url = $(elementTarget).find('img').attr('src');
                Service.download(url);
            },
            'image.edit': function (elementTarget, event) {
                showImageProcessorModal(elementTarget);
                // Service.Image.edit(function (success, data) {
                    
                // });
            },
            'image.rename': function (elementTarget, event) {
                showRenameFileModal(elementTarget);
            },
            'image.delete': function (elementTarget, event) {
                showDeleteFileModal(elementTarget);
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
            showCreateSubfolderModal($('.folder-selected')[0]);
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

            let path = buttonThis.attr('data-path');
            if (path) {
                showImages(path);
            }
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
