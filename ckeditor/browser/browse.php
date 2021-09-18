<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" integrity="sha512-aOG0c6nPNzGk+5zjwyJaoRUgCdOrfSDhmMID2u4+OIslr0GjpLKo7Xm0Ao3xmpM4T8AmIouRkqwj1nrdVsLKEQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.1/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="./css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/context-menu.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" integrity="sha384-eMNCOe7tC1doHpGoWe/6oMVemdAVTMs2xqW4mwXrXsW0L84Iytr2wi5v2QjrP/xp" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.min.js" integrity="sha384-cn7l7gDp0eyniUwwAZgrzD06kc/tftFf19TOAs2zVinnD/C7E91j9yyk5//jjpt/" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.11.1/js/jquery.dataTables.min.js"></script>
    <script src="./js/libs/caman.full.min.js"></script>
    <script src="./js/globals.js"></script>
    <script src="./js/context.js"></script>
    <script src="./js/filters.js"></script>
    <script src="./js/image-editor.js"></script>
    <script src="./js/services.js"></script>
    <script src="./js/plugins.js"></script>
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
                <!-- Upload -->
                <button class="btn btn-light bg-white border border-secondary upload">
                    <i class="bi bi-upload"></i>
                    <span>Upload</span>
                </button>
                <!-- Hide input file -->
                <input type="file" name="uploadfile" id="uploadfile" style="display: none;" />
                <!-- New Subfolder -->
                <button class="btn btn-light bg-white border border-secondary subfolder">
                    <i class="bi bi-folder-plus"></i>
                    <span>New Subfolder</span>
                </button>
                <!-- Maximize Screen -->
                <button class="btn btn-light bg-white border border-secondary maximize-screen">
                    <i class="bi bi-arrows-fullscreen"></i>
                    <span>Maximize</span>
                </button>
                <!-- Minimize Screen -->
                <button class="btn btn-light bg-white border border-secondary minimize-screen hidden">
                    <i class="bi bi-fullscreen-exit"></i>
                    <span>Minimize</span>
                </button>
            </div>
            <div class="col-4">
                <div class="row">
                    <!-- Search Button -->
                    <input type="text" name="search" id="search" placeholder="Search..." class="form-control col shadow-none" />
                    <!-- Setting Button -->
                    <button class="btn btn-light col-2 shadow-none" data-bs-toggle="offcanvas" data-bs-target="#offcanvasSetting" aria-controls="offcanvasSetting">
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

    <!-- Right Transition Modal -->
    <div class="offcanvas offcanvas-end settings-modal" data-bs-scroll="false" data-bs-backdrop="false" tabindex="-1" id="offcanvasSetting" aria-labelledby="offcanvasSettingLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasSettingLabel">Settings</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <!-- Settings -->
            <div class="settings">
                <label class="mb-2">Settings</label>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="fds-setting[]" id="filename-setting" data-setting="filename" data-group="fds-setting" checked>
                    <label class="form-check-label" for="filename-setting">
                        File Name
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" name="fds-setting[]" id="date-setting" data-setting="date" data-group="fds-setting" checked>
                    <label class="form-check-label" for="date-setting">
                        Date
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="fds-setting[]" id="filesize-setting" data-setting="filesize" data-group="fds-setting" checked>
                    <label class="form-check-label" for="filesize-setting">
                        File Size
                    </label>
                </div>
            </div>
            <!-- View -->
            <div class="view mt-3">
                <label class="mb-2">View</label>
                <div class="form-check mb-2">
                    <input class="form-check-input view-setting" type="radio" value="list" name="view-setting" id="list-view-setting" data-setting="view">
                    <label class="form-check-label" for="list-view-setting">
                        List
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input view-setting" type="radio" value="thumbnail" name="view-setting" id="thumbnail-view-setting" data-setting="view" checked>
                    <label class="form-check-label" for="thumbnail-view-setting">
                        Thumbnail
                    </label>
                </div>
                <div class="form-check mb-2">
                    <input class="form-check-input view-setting" type="radio" value="compact" name="view-setting" id="compact-view-setting" data-setting="view">
                    <label class="form-check-label" for="compact-view-setting">
                        Compact
                    </label>
                </div>
            </div>
            <!-- Sort By -->
            <div class="sortby mt-3">
                <label class="mb-2">Sort by</label>
                <select class="form-select form-select-sm" name="sorby-setting" id="sorby-setting" data-setting="sortby" aria-label=".form-select-sm sortby-setting">
                    <option value="filename" selected>File Name</option>
                    <option value="filesize">File Size</option>
                    <option value="date">Date</option>
                </select>
            </div>
            <!-- Order By -->
            <div class="orderby mt-3">
                <label class="mb-2">Order by</label>
                <div class="form-check mb-2">
                    <input class="form-check-input orderby-setting" type="radio" value="asc" name="orderby-setting" id="asc-orderby-setting" data-setting="orderby" checked>
                    <label class="form-check-label" for="asc-orderby-setting">
                        Ascending
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input orderby-setting" type="radio" value="desc" name="orderby-setting" id="desc-orderby-setting" data-setting="orderby">
                    <label class="form-check-label" for="desc-orderby-setting">
                        Descending
                    </label>
                </div>
            </div>
            <!-- Thumbnail Sise -->
            <div class="mt-3 mb-3">
                <label class="form-label mb-2">Thumbnail Size</label>
                <div class="d-flex flex-row align-items-center justify-content-between">
                    <input type="text" class="form-control form-control-sm me-4 text-center" id="thumbsize-setting" name="thumbsize-setting" value="150" data-setting="thumbsize" data-target="#slider-thumbsize-setting" data-group="thumbsize-setting">
                    <input type="range" class="form-range" id="slider-thumbsize-setting" name="slider-thumbsize-setting" min="150" max="500" step="4" value="150" data-setting="thumbsize" data-target="#thumbsize-setting" data-group="thumbsize-setting">
                </div>
            </div>
        </div>
    </div>
    <!-- ./Right Transition Modal -->

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

    <div id="context-copy-move" class="context-menu"></div>

<script>
    function showImages(path) {
        path = path || $('.folder-selected').attr('data-path');
        $.ajax({
            url: '../uploader/do_file.php',
            method: 'POST',
            data: { path, action: 'read' },
            dataType: 'json',
            success: function (response) {
                if (response && response.success) {
                    let count = 1;
                    let dataResponse = response.data;

                    // If it is List View, show grid type
                    if (ACTIVE_SETTINGS.view == 'list') {
                        $('.main-content').html('<table id="datatable" class="hover row-border images"></table>');
                        let columnDefs = THEME.columnDefs().map(function (row) {
                            let column = THEME.getColumnByIndex(row.targets);
                            if (ACTIVE_SETTINGS.hasOwnProperty(column)) {
                                // Default column is always visible as File Name
                                row.visible = THEME.alwaysVisibleColumns.indexOf(column) !== -1 ? true : ACTIVE_SETTINGS[column];
                            }
                            return row;
                        });
                        THEME.addOption({
                            order: [[ THEME.indexesColumn[ACTIVE_SETTINGS.sortby], ACTIVE_SETTINGS.orderby ]],
                            data: dataResponse,
                            columnDefs: columnDefs,
                            // @Hook: fired after row has already been created
                            createdRow: function(row, data, dataIndex) {
                                $(row).addClass('wrap-image context-menu-target');
                                $(row).attr('data-ctx-item-type', 'image');
                                $(row).attr('data-mime', data.mine);
                                $(row).attr('data-path', `${data.folder}/${data.basename}`);
                                $(row).attr('data-src', data.src);
                            }
                        }).init('#datatable');
                        // Restart context menu for binding images to it
                        bindContextMenu();
                        return;
                    }

                    // SortBy & OrderBy images
                    dataResponse.sort(function (prev, next) {
                        let orderby = ACTIVE_SETTINGS.orderby == 'asc' ? 1 : -1;
                        if (ACTIVE_SETTINGS.sortby == 'filename') {
                            prev = prev.filename.toUpperCase();
                            next = next.filename.toUpperCase();
                            return prev == next ? 0 : (prev < next ? -orderby : orderby);
                        } else if (ACTIVE_SETTINGS.sortby == 'filesize') {
                            prev = new Date(prev.size);
                            next = new Date(next.size);
                            return prev == next ? 0 : (prev < next ? -orderby : orderby);
                        } else {
                            prev = new Date(prev.modified);
                            next = new Date(next.modified);
                            return prev == next ? 0 : (prev < next ? -orderby : orderby);
                        }
                    });

                    let out = '<div class="images">';
                    dataResponse.forEach(function (info, idx) {
                        if (count === 1) {
                            out += '<div class="row row-image">';
                        }
                        out += '<div class="col-3 block-image">';
                        out +=      `<div class="wrap-image wrap-image-bg context-menu-target" data-mime="${info.mime}" data-path="${info.folder}/${info.basename}" data-src="${info.src}" data-ctx-item-type="image">`;
                        if (ACTIVE_SETTINGS.view == 'thumbnail') {
                            out +=          `<img class="image" src="${info.src}" height="100" width="100%" alt="${info.filename}" />`;
                            out +=          `<div class="fname filename">${info.basename}</div>`;
                            out +=          `<div class="fmodified date">${info.modified}</div>`;
                            out +=          `<div class="fsize filesize">${info.formated_size}</div>`;
                        } else {
                            out +=          `<div class="fname filename"><i class="bi bi-image-fill"></i> ${info.basename}</div>`;
                        }
                        out +=      '</div>';
                        out += '</div>';
                        if (count === 4) {
                            count = 0;
                            out += '</div>';
                        }
                        count++;
                    });
                    out += '</div>';

                    // Add to DOM
                    $('.main-content').html(out);

                    // Restart context menu for binding images to it
                    bindContextMenu();

                    // Update thumbnail layout
                    if (ACTIVE_SETTINGS.view == 'thumbnail') {
                        THEME.toggleFileNameSizeDate();
                        THEME.updateThumbnail(ACTIVE_SETTINGS.thumbsize);
                    }
                    $('.wrap-image img').draggable({
                        helper: 'clone',
                        // disable: true,
                        start(e, ui) {
                            let elem = $(ui.helper);
                            elem.css('marginTop', $(e.currentTarget).height() / 2);
                            elem.css('marginLeft', $(e.currentTarget).width() * 2 / 3);
                            elem.css('border', '5px solid #ffffff')
                            elem.css('transform', 'rotate(-20deg)')
                            elem.attr('width', '40px');
                            elem.attr('height', '40px');
                        },
                        drag(e, ui) {
                            ui.offset.left = e.pageX || e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft || 0);
                            ui.offset.top  = e.pageY || e.clientY + (document.documentElement.scrollTop  || document.body.scrollTop  || 0);
                        },
                        stop(e, ui) {
                            console.log('stop => ', ui)
                        }
                    });

                    $('.sidebar button').droppable({
                        accept: '.wrap-image img',
                        // classes: {
                        //     "ui-droppable-active": "ac",
                        //     "ui-droppable-hover": "hv"
                        // },
                        // The dragging element starts dragging
                        activate(e, ui) {
                            //
                        },
                        // The dragging element stopped dragging
                        deacrivate(e, ui) {
                            //
                        },
                        // The dragging element moved on target element
                        over(e, ui) {
                            // Highlight folder
                            $(this).css('background', '#ffffff');
                        },
                        // The dragging element moved out of target element
                        out(e, ui) {
                            // Turn off highlight folder
                            $(this).css('background', 'transparent');
                        },
                        // Element moved on target element and dropped
                        drop(e, ui) {
                            // Turn off highlight folder
                            $(this).css('background', 'transparent');
                            // let offset = $(this).offset();
                            // $('#context-copy-move').offset({
                            //     top: offset.top + $(this).height(),
                            //     left: offset.left + $(this).width() / 2
                            // });
                            // // Turn on menu context
                            // $('#context-copy-move').addClass('context-menu--active');
                            // // Turn off menu context
                            // document.addEventListener('click', function (e) {
                            //     $('#context-copy-move').removeAttr('style');
                            //     $('#context-copy-move').removeClass('context-menu--active');
                            // });

                            ContextMenu.show({
                                selectorContext: '#context-copy-move',
                                mouseEvent: e,
                                types: {
                                    copymove: [
                                        {
                                            id: 'copy',
                                            icon: '<i class="bi bi-files"></i>',
                                            label: 'Copy Here'
                                        },
                                        {
                                            id: 'move',
                                            icon: '<i class="bi bi-box-arrow-right"></i>',
                                            label: 'Move Here'
                                        },
                                    ]
                                },
                                activations: {},
                                actions: {
                                    'copymove.copy': function (elementTarget, event) {
                                        console.log('copy')
                                    },
                                    'copymove.move': function (elementTarget, event) {
                                        console.log('move')
                                    },
                                }
                            }, 'copymove', this);
                        }
                    });
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
        load('./modals/newedit.html', function (classNameWrap) {
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
    }

    function showRenameFolderModal(elementTarget) {
        // Mark the selected current folder
        var selectedCurrentFolderClass = markCurrentFolderSelected(elementTarget);
        // Selected folder
        var folderSelected = $(elementTarget).attr('data-path');
        // Create temporary DOM for loading modal
        load('./modals/newedit.html', function (classNameWrap) {
            function loadDataOnInput(modal) {
                let input = modal.find('#input');
                input.val(getFileName(folderSelected));
                // Focus after 1s
                setTimeout(function() {
                    input.focus();
                }, 1000);
            }
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
    }

    // TODO: Handle more after folder deleted
    function showDeleteFolderModal(elementTarget) {
        // Mark the selected current folder
        var selectedCurrentFolderClass = markCurrentFolderSelected(elementTarget);
        var folderDeleted = $(elementTarget).data('path');
        // Create temporary DOM for loading modal
        load('./modals/confirmation.html', function (classNameWrap) {
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
    }

    function showRenameFileModal(elementTarget) {
        let oldFile = $(elementTarget).attr('data-path');
        // Create temporary DOM for loading modal
        load('./modals/newedit.html', function (classNameWrap) {
            function loadDataOnInput(modal) {
                let input = modal.find('#input');
                input.val(getFileName(oldFile));
                // Focus after 1s
                setTimeout(function() {
                    input.focus();
                }, 1000);
            }

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
    }

    // TODO: Handle more when folder has subfolders
    function showDeleteFileModal(elementTarget) {
        let path = $(elementTarget).attr('data-path');
        // Add temporary DOM for loading modal
        load('./modals/confirmation.html', function (classNameWrap) {
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
    }

    function showImageViewModal(elementTarget) {
        var selectedCurrentFolder = $('.sidebar .folder-selected').attr('data-path');
        var srcImgSelected = $(elementTarget).find('img').attr('src');

        load('./modals/slideshow.html', function (classNameWrap) {
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
    }

    function showImageProcessorModal(elementTarget) {
        var pathImageSelected = $(elementTarget).attr('data-path');
        var srcImageSelected  = $(elementTarget).attr('data-src') || $(elementTarget).find('img').attr('src');

        load('./modals/image-processor.html', function (classNameWrap) {
            var modal = $('.' + classNameWrap).find('.modal-app');

            var CANVAS_ID = '#canvas';
            var CANVAS_HEIGHT = 400;
            var CANVAS_WIDTH  = 500;
            var CROP_BOX_WIDTH  = 200;
            var CROP_BOX_HEIGHT = 200;
            var IS_CHANGED_DATA = false;

            var SLIDERS = {
                '#slider-blur'      : {min: -100, max: 100, val: 0},
                '#slider-brightness': {min: -100, max: 100, val: 0},
                '#slider-contrast'  : {min: -100, max: 100, val: 0},
                '#slider-saturation': {min: -100, max: 100, val: 0},
                '#slider-exposure'  : {min: -100, max: 100, val: 0},
                '#slider-sepia'     : {min: 0, max: 100, val: 0},
                '#slider-sharpen'   : {min: 0, max: 100, val: 0}
            };

            // Load image to canvas
            modal.find(CANVAS_ID).attr('src', srcImageSelected);

            var degrees = 0;
            var oriImgWidth  = modal.find(CANVAS_ID).width();
            var oriImgHeight = modal.find(CANVAS_ID).height();
            var curImgWidth  = oriImgWidth  > CANVAS_WIDTH  ? CANVAS_WIDTH  : oriImgWidth;;
            var curImgHeight = oriImgHeight > CANVAS_HEIGHT ? CANVAS_HEIGHT : oriImgHeight;;

            // Crop image
            var cropBox     = modal.find('.crop-box');
            var event_state = {};
            var constraint  = isKeepRatio('input[name="crop-keep-ratio"]');
            var caman = Caman(CANVAS_ID);

            // Update image width & height in resize tool
            modal.find('input[name="resize-w"]').val(oriImgWidth);
            modal.find('input[name="resize-h"]').val(oriImgHeight);
            // Set default size of image
            modal.find(CANVAS_ID).attr('width', `${curImgWidth}px`);
            modal.find(CANVAS_ID).attr('height', `${curImgHeight}px`);

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
                Object.keys(SLIDERS).forEach(function (identity) {
                    $(identity).slider({
                        orientation: "horizontal", // vertical
                        animate: true,
                        range: 'min',
                        min: SLIDERS[identity].min,
                        max: SLIDERS[identity].max,
                        value: SLIDERS[identity].val,
                        step: 1,
                        change: function (event, ui) {
                            enableResetBtn();
                            // Update slider value
                            $(this).attr('data-val', ui.value);
                            if (event.originalEvent === undefined) {
                                return;
                            }
                            applyFilters();
                            caman.render();
                        },
                        slide: function (event, ui) {
                            // Update slider value in real time when sliding seed
                            $(this).find('.ui-slider-handle').text(ui.value);
                        },
                        create: function (event, ui) {
                            var value = $(this).slider('value');
                            // Show default value of sliders
                            $(this).find('.ui-slider-handle').text(value);
                            // Set default data of sliders
                            $(this).attr('data-val', value);
                        }
                    });
                });

                // Show image presets
                modal.find('.presets-img').each(function (idx, element) {
                    let idPresetImg = '#' + $(element).attr('id');
                    let preset = $(element).data('preset');
                    // Load selected image to presets
                    $(element).attr('src', srcImageSelected);
                    // Apply the specified preset filters on each image
                    Caman(idPresetImg, function() {
                        if (typeof this[preset] === 'function') {
                            this[preset]().render();
                        } else {
                            console.error(`Preset [${preset}] not exists.`);
                        }
                    });
                    // Apply selected filter to canvas when selecting a preset
                    $(element).parent().on('click', function (e) {
                        e.preventDefault();
                        let preset = $(this).find('canvas').data('preset');
                        if (typeof caman[preset] === 'function') {
                            resetFilters();
                            caman.revert(true);
                            caman[preset]();
                            caman.render();
                        }
                    });
                });
            });

            // Reset button
            modal.find('.reset').on('click', function (e) {
                e.preventDefault();
                caman.reset();
                caman.render();
                resetFilters();
                resetCropBox();
            });

            // Save button
            modal.find('.save').on('click', function (e) {
                var processingModal;
                var isSaved = false;
                loadModal('./modals/processing.html', function (_wrapClassName, _modal) {
                    processingModal = _modal;
                    // Hide all modals, reload images after click on OK button
                    processingModal.on('click', '.ok', function () {
                        // Hide all modals
                        processingModal.modal('hide');
                        isSaved && modal.modal('hide');
                        // Reload image area
                        let selectedCurrentFolder = $('.folder-selected').attr('data-path');
                        showImages(selectedCurrentFolder);
                    });
                }, 'wrap-modal-processing', 1052);

                let blob = base64ToBlob(caman.toBase64());
                let folderSelected = basepath(pathImageSelected);
                let filename = getFileName(pathImageSelected);
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
                        let bodyModal = processingModal.find('.message');
                        // Turn off loading spiner
                        bodyModal.find('.loader').hide();
                        // Show message
                        if (response && response.success) {
                            isSaved = true;
                            bodyModal.html('<p class="success m-0">Image already saved successfully.</p>');
                        } else {
                            bodyModal.html('<p class="error m-0">'+response.error+'</p>');
                        }
                        // Show OK button
                        processingModal.find('.modal-footer').show();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        processingModal.find('.message').find('.loader').hide();
                        processingModal.find('.message').html('<p class="error m-0">'+jqXHR.responseText+'</p>');
                        processingModal.find('.modal-footer').show();
                    }
                });
            });

            // Toogle crop box when selecting it
            modal.find('.container-collapse').on('shown.bs.collapse', function (e) {
                let tool = $(this).parent().data('tool');
                if (tool == 'crop') {
                    // Align center for crop box
                    alignCenterCropBox();
                    modal.find('.crop-box').show();
                } else {
                    modal.find('.crop-box').hide();
                }
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
                caman.resize({width: curImgWidth, height: curImgHeight});
                applyFilters();
                caman.render();
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
                degrees += 90;
                caman.rotate(90);
                applyFilters();
                caman.render();
            });

            // Rotating image by counterclockwise
            modal.on('click', '.apply-counterclockwise', function(e) {
                degrees -= 90;
                caman.rotate(-90);
                applyFilters();
                caman.render();
            });

            // Crop image
            modal.on('click', '.apply-crop', function (e) {
                var left = cropBox.offset().left - modal.find(CANVAS_ID).offset().left;
                var top  = cropBox.offset().top  - modal.find(CANVAS_ID).offset().top;
                curImgWidth  = cropBox.width();
                curImgHeight = cropBox.height();
                // Crop image
                caman.crop(curImgWidth, curImgHeight, left, top);
                applyFilters();
                caman.render();
                // Align center for crop box
                alignCenterCropBox();
            });

            // Keep aspect ratio of image
            modal.find('input[name="crop-keep-ratio"]').on('change', function () {
                constraint  = isKeepRatio('input[name="crop-keep-ratio"]');
            });

            // Drag crop box
            cropBox.draggable({
                scroll: true,
                axis: "xy",
                containment: CANVAS_ID,
                revert: false,
                disable: false,
                start: function(event, ui) {
                    // when starting drag
                },
                drag: function(event, ui) {
                    // when dragging
                },
                stop:function(event, ui) {
                    // Dragging done
                }
            });

            // Start event for corners (8) of crop box
            cropBox.on('mousedown touchstart', '.crop-handle', function (e) {
                e.preventDefault();
                e.stopPropagation();

                saveEventState(e);

                // When press and holder a corner of crop box via mouse
                modal.on('mousemove touchmove', resizeCropBox);
                // When release the mouse
                modal.on('mouseup touchend', endResizeCropBox);
            });

            // Save state of event that it starts pulling corners of crop box
            function saveEventState(e) {
                // Save the initial event details and container state
                event_state.container_width  = cropBox.width();
                event_state.container_height = cropBox.height();
                event_state.container_left   = cropBox.offset().left;
                event_state.container_top    = cropBox.offset().top;
                event_state.canvas_width     = $(CANVAS_ID).width();
                event_state.canvas_height    = $(CANVAS_ID).height();
                event_state.canvas_left      = $(CANVAS_ID).offset().left;
                event_state.canvas_top       = $(CANVAS_ID).offset().top;
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
            function resizeCropBox(e) {
                // e.preventDefault();

                // Turn off draggling crop box when pulling its corners
                cropBox.draggable({disable: true});

                var mouse={},
                    width,
                    height,
                    left,
                    top,
                    maxWidth,
                    maxHeight,
                    //offset=cropBox.offset(),
                    $currentCropHandle=$(event_state.event.target);
                mouse.x = (e.clientX || e.pageX || e.originalEvent.touches[0].clientX) + $(window).scrollLeft();
                mouse.y = (e.clientY || e.pageY || e.originalEvent.touches[0].clientY) + $(window).scrollTop();

                // Position that crop box differently depending on the corner dragged and constraints
                if ($currentCropHandle.hasClass('crop-point-bottom-right')) { // se
                    width  = mouse.x - event_state.container_left;
                    height = mouse.y - event_state.container_top;
                    left   = event_state.container_left;
                    top    = event_state.container_top;
                    // If width/height of crop box is over canvas width/height, set it inside canvas
                    let dxCanvasCropBox = left - event_state.canvas_left;
                    let dyCanvasCropBox = top  - event_state.canvas_top;
                    maxWidth  = event_state.canvas_width  - dxCanvasCropBox;
                    maxHeight = event_state.canvas_height - dyCanvasCropBox;
                    if (width > maxWidth) {
                        width = maxWidth;
                    }
                    if (height > maxHeight) {
                        height = maxHeight;
                    }
                } else if ($currentCropHandle.hasClass('crop-point-bottom-left')) { // sw
                    width  = event_state.container_width - (mouse.x - event_state.container_left);
                    height = mouse.y - event_state.container_top;
                    left   = mouse.x;
                    top    = event_state.container_top;
                    // If width/height of crop box is over canvas width/height, set it inside canvas
                    let dxCanvasCropBox = event_state.canvas_left + event_state.canvas_width - event_state.container_width - event_state.container_left;
                    let dyCanvasCropBox = top  - event_state.canvas_top;
                    maxWidth  = event_state.canvas_width  - dxCanvasCropBox;
                    maxHeight = event_state.canvas_height - dyCanvasCropBox;
                    if (width < 0) {
                        width = 0;
                        left = maxWidth + event_state.canvas_left;
                    } else if (width > maxWidth) {
                        width = maxWidth;
                        left  = event_state.canvas_left;
                    }
                    if (height > maxHeight) {
                        height = maxHeight;
                    }
                } else if ($currentCropHandle.hasClass('crop-point-top-left')) { // nw
                    width  = event_state.container_width  - (mouse.x - event_state.container_left);
                    height = event_state.container_height - (mouse.y - event_state.container_top);
                    left   = mouse.x;
                    top    = mouse.y;
                    // When press and hold SHIFT
                    if (constraint || e.shiftKey) {
                        top = mouse.y - ((width / event_state.container_width * event_state.container_height) - height);
                    }
                    // If width/height of crop box is over canvas width/height, set it inside canvas
                    let dxCanvasCropBox = event_state.canvas_left + event_state.canvas_width  - event_state.container_width  - event_state.container_left;
                    let dyCanvasCropBox = event_state.canvas_top  + event_state.canvas_height - event_state.container_height - event_state.container_top;
                    maxWidth  = event_state.canvas_width  - dxCanvasCropBox;
                    maxHeight = event_state.canvas_height - dyCanvasCropBox;
                    if (width < 0) {
                        width = 0;
                        left = maxWidth + event_state.canvas_left;
                    } else if (width > maxWidth) {
                        width = maxWidth;
                        left  = event_state.canvas_left;
                    }
                    if (height < 0) {
                        height = 0;
                        top = maxHeight + event_state.canvas_top;
                    } else if (height > maxHeight) {
                        height = maxHeight;
                        top = event_state.canvas_top;
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
                    // If width/height of crop box is over canvas width/height, set it inside canvas
                    let dxCanvasCropBox = left - event_state.canvas_left;
                    let dyCanvasCropBox = event_state.canvas_top + event_state.canvas_height - event_state.container_top   - event_state.container_height;
                    maxWidth  = event_state.canvas_width  - dxCanvasCropBox;
                    maxHeight = event_state.canvas_height - dyCanvasCropBox;
                    if (width > maxWidth) {
                        width = maxWidth;
                    }
                    if (height < 0) {
                        height = 0;
                        top = maxHeight + event_state.canvas_top;
                    } else if (height > maxHeight) {
                        height = maxHeight;
                        top = event_state.canvas_top;
                    }
                } else if ($currentCropHandle.hasClass('crop-point-top')) { // n
                    width  = event_state.container_width;
                    height = event_state.container_height - (mouse.y - event_state.container_top);
                    left   = event_state.container_left;
                    top    = mouse.y;
                    // If width/height of crop box is over canvas width/height, set it inside canvas
                    let dyCanvasCropBox = event_state.canvas_top + event_state.canvas_height - event_state.container_top   - event_state.container_height;
                    maxHeight = event_state.canvas_height - dyCanvasCropBox;
                    if (height < 0) {
                        height = 0
                        top = maxHeight + event_state.canvas_top;
                    } else if (height > maxHeight) {
                        height = maxHeight;
                        top = event_state.canvas_top;
                    }
                } else if ($currentCropHandle.hasClass('crop-point-bottom')) { // s
                    width  = event_state.container_width;
                    height = mouse.y - event_state.container_top;
                    left   = event_state.container_left;
                    top    = event_state.container_top;
                    // If width/height of crop box is over canvas width/height, set it inside canvas
                    let dyCanvasCropBox = event_state.container_top - event_state.canvas_top;
                    maxHeight = event_state.canvas_height - dyCanvasCropBox;
                    if (height > maxHeight) {
                        height = maxHeight;
                    }
                } else if ($currentCropHandle.hasClass('crop-point-left')) { // w
                    width  = event_state.container_width - (mouse.x - event_state.container_left);
                    height = event_state.container_height;
                    left   = mouse.x;
                    top    = event_state.container_top;
                    // If width/height of crop box is over canvas width/height, set it inside canvas
                    let dxCanvasCropBox = event_state.canvas_left + event_state.canvas_width - event_state.container_left - event_state.container_width;
                    maxWidth = event_state.canvas_width - dxCanvasCropBox;
                    if (width < 0) {
                        width = 0;
                        left  = event_state.container_left + event_state.container_width;
                    } else if (width > maxWidth) {
                        width = maxWidth;
                        left  = event_state.canvas_left;
                    }
                } else if ($currentCropHandle.hasClass('crop-point-right')) { // e
                    width  = mouse.x - event_state.container_left;
                    height = event_state.container_height;
                    left   = event_state.container_left;
                    top    = event_state.container_top;
                    // If width/height of crop box is over canvas width/height, set it inside canvas
                    let dxCanvasCropBox = event_state.container_left - event_state.canvas_left;
                    maxWidth = event_state.canvas_width - dxCanvasCropBox;
                    if (width > maxWidth) {
                        width = maxWidth;
                    }
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
            function endResizeCropBox(e) {
                // e.preventDefault();
                // Turn off pulling corners of crop box
                modal.off('mouseup touchend', endResizeCropBox);
                modal.off('mousemove touchmove', resizeCropBox);
                // Turn on draggling crop box when finished pulling its corners
                cropBox.draggable({disable: false});
            }

            function alignCenterCropBox(wCanvas, hCanvas) {
                let topCropBoxHandleSeed = 4;
                wCanvas = wCanvas || $(CANVAS_ID).width();
                hCanvas = hCanvas || $(CANVAS_ID).height();
                cropBox.css('width', wCanvas);
                cropBox.css('height', hCanvas);
                cropBox.css('top', `calc(50% - ${hCanvas + topCropBoxHandleSeed}px)`);
                cropBox.css('left', `50%`);
                cropBox.css('transform', `translate(-50%, -50%)`);
            }

            function resetCropBox() {
                cropBox.css('width', `${CROP_BOX_WIDTH}px`);
                cropBox.css('height', `${CROP_BOX_HEIGHT}px`);
                cropBox.css('top', `calc(50% - ${CANVAS_HEIGHT}px)`);
                cropBox.css('left', `50%`);
                cropBox.css('transform', `translate(-50%, -50%)`);
            }

            function enableResetBtn() {
                if (!IS_CHANGED_DATA) {
                    IS_CHANGED_DATA = true;
                    modal.find('.reset').removeClass('disabled').show();
                }
            }

            function applyFilters() {
                caman.revert(false);
                Object.keys(SLIDERS).forEach(function (identity) {
                    let filter = $(identity).data('type');
                    let value  = $(identity).attr('data-val');
                    if (value == 0) {
                        return;
                    }
                    if (typeof caman[filter] === 'function') {
                        caman[filter](value);
                    } else {
                        console.error(`Filter [${filter}] not exists.`);
                    }
                });
            }

            function resetFilters() {
                Object.keys(SLIDERS).forEach(function (identity) {
                    let silder = $(identity);
                    let value = SLIDERS[identity].val;
                    // Resetn the value data, default value and default text of silder
                    silder.attr('data-val', value);
                    silder.slider('option', 'value', value);
                    silder.find('.ui-slider-handle').text(value);
                });
            }

            function isKeepRatio(identity) {
                return modal.find(identity).is(':checked');
            }

            function ratioImage() {
                return oriImgHeight / oriImgWidth;
            }
        });
    }

    function bindContextMenu() {
        ContextMenu.start(configs);
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
            },
            'image.rename': function (elementTarget, event) {
                showRenameFileModal(elementTarget);
            },
            'image.delete': function (elementTarget, event) {
                showDeleteFileModal(elementTarget);
            }
        }
    };

    // Settings Modal
    var SETTINGS = {
        click: [
            '#filename-setting',
            '#filesize-setting',
            '#date-setting',
            '.view-setting',
            '.orderby-setting',
        ],
        change: [
            '#thumbsize-setting',
            '#slider-thumbsize-setting',
            '#sorby-setting'
        ],
    };

    var ACTIVE_SETTINGS = {
        filename: true,
        filesize: true,
        date: true,
        view: 'thumbnail',  // list | thumbnail | compact
        orderby: 'asc',     // asc | desc
        sortby: 'filename', // filename | filesize | date
        thumbsize: 150
    };

    var THEME = {
        identity: null,
        datatable: null,
        indexesColumn: {
            icon: 0,
            filename: 1,
            filesize: 2,
            date: 3
        },
        alwaysVisibleColumns: [
            'filename'
        ],
        options: {
            // stateSave: true,
            autoWidth: false,
            paging: false,
            searching: false,
            info: false,
        },
        getColumnByIndex(index) {
            let columns = Object.keys(this.indexesColumn);
            for (let i=0; i < columns.length; i++) {
                if (this.indexesColumn[columns[i]] === index) {
                    return columns[i];
                }
            }
            return false;
        },
        columnDefs() {
            return [
                {
                    targets: this.indexesColumn.icon,
                    orderable: false,
                    title: undefined,
                    width: '10px',
                    render(data, type, row, meta) {
                        return `
                            <i class="bi bi-image-fill" style="font-size:25px;"></i>
                        `;
                    }
                },
                {
                    targets: this.indexesColumn.filename,
                    title: 'File Name',
                    data: 'basename',
                    visible: true,
                    render(data, type, row, meta) {
                        return `
                            <div class="target-highlight">${row.filename}</div>
                        `;
                    }
                },
                {
                    targets: this.indexesColumn.filesize,
                    title: 'File Size',
                    data: 'size',
                    visible: true,
                    type: 'only-numeric',
                    render(data, type, row, meta) {
                        return `${row.formated_size}`;
                    }
                },
                {
                    targets: this.indexesColumn.date,
                    title: 'Date',
                    data: 'modified',
                    visible: true,
                },
            ];
        },
        addOption: function (options) {
            var self = this;
            options = options || [];
            if (typeof options === 'object') {
                if (!Array.isArray(options)) {
                    options = [options];
                }
                options.forEach(function (option) {
                    Object.keys(option).forEach(function (key) {
                        self.options[key] = option[key];
                    })
                })
            }
            return this;
        },
        init: function (identity) {
            this.identity  = identity;
            // Set default for columnDefs if not found
            if (!this.options.hasOwnProperty('columnDefs')) {
                this.options.columnDefs = this.columnDefs();
            }
            this.datatable = $(this.identity).DataTable(this.options);
        },
        destroy: function () {
            if (this.datatable) {
                this.datatable.destroy();
                $(this.identity).empty();
            }
        },
        redraw: function () {
            this.datatable = $(this.identity).DataTable(this.options).draw();
        },
        updateThumbnail: function(value) {
            let blocks = $('.images').find('.block-image');
            if (isNaN(value)) {
                value = 150;
            }
            value = parseInt(value);
            value = value < 150 ? value : value;
            if (value < 200) {
                blocks.removeClass('col-3');
                blocks.removeClass('col-6');
                blocks.removeClass('col-12');
                blocks.addClass('col-3');
                blocks.find('img').attr('height', 100);
            } else if (value < 400) {
                blocks.removeClass('col-3');
                blocks.removeClass('col-6');
                blocks.removeClass('col-12');
                blocks.addClass('col-6');
                blocks.find('img').attr('height', value);
            } else {
                blocks.removeClass('col-3');
                blocks.removeClass('col-6');
                blocks.removeClass('col-12');
                blocks.addClass('col-12');
                blocks.find('img').attr('height', value);
            }
        },
        toggleColumns: function () {
            if (this.datatable) {
                let self = this;
                let columnDefs = this.columnDefs().map(function (row) {
                    let column = THEME.getColumnByIndex(row.targets);
                    if (ACTIVE_SETTINGS.hasOwnProperty(column)) {
                        // Default column is always visible as File Name
                        row.visible = self.alwaysVisibleColumns.indexOf(column) !== -1 ? true : ACTIVE_SETTINGS[column];
                    }
                    return row;
                });
                this.addOption({columnDefs});
                this.destroy();
                this.datatable = $(this.identity).DataTable(this.options).draw();
            }
        },
        toggleFileNameSizeDate: function (targetSetting) {
            if (targetSetting) {
                let toggle = ACTIVE_SETTINGS[targetSetting] ? 'show' : 'hide';
                $('.images').find(`.${targetSetting}`)[toggle]();
            } else {
                ['filename', 'filesize', 'date'].forEach(function (target) {
                    let toggle = ACTIVE_SETTINGS[target] ? 'show' : 'hide';
                    $('.images').find(`.${target}`)[toggle]();
                });
            }
        }
    };

    $(function() {
        // Bind context menu
        bindContextMenu();

        // Maximize screen (fullscreen)
        $('.maximize-screen').on('click', function (e) {
            e.preventDefault();
            openFullScreen();
            $(this).hide();
            $('.minimize-screen').show();
        });

        // Minimize screen (exit fullscreen)
        $('.minimize-screen').on('click', function (e) {
            e.preventDefault();
            closeFullScreen();
            $(this).hide();
            $('.maximize-screen').show();
        });

        $(window).on('resize', function (e) {
            // Show fullscreen button if exit fullscreen
            if (!isFullScreen() && $('.maximize-screen').is(':hidden')) {
                $('.minimize-screen').hide();
                $('.maximize-screen').show();
            }
        });

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
        $(document).on('click', '.upload', function (e) {
            // Open dialog for choosing upload file
            $('#uploadfile').click();
        });

        // Subfolder (modal)
        $(document).on('click', '.subfolder', function () {
            showCreateSubfolderModal($('.folder-selected')[0]);
        })

        // Click on each image
        $(document).on('click', '.images .wrap-image', function() {
            if (ACTIVE_SETTINGS.view == 'list') {
                $(this).closest('.images').find('.target-highlight').removeClass('image-selected');
                $(this).find('.target-highlight').addClass('image-selected');
            } else {
                $(this).closest('.images').find('.wrap-image').removeClass('image-selected');
                $(this).addClass('image-selected');
            }
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
                    $('.modal .cancelok').show();
                    $('.modal .uploaderror').html(jqXHR.responseText);
                    $('.modal .loader').hide();
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

        // Handling events in settings
        Object.keys(SETTINGS).forEach(function (event) {
            SETTINGS[event].forEach(function (identity) {
                $(identity).on(event, function (e) {
                    let setting = $(this).data('setting');
                    let type    = $(this).attr('type');
                    let group   = $(this).attr('data-group');
                    let value   = type == 'checkbox' ? $(this).is(':checked') : $(this).val();

                    ACTIVE_SETTINGS[setting] = value;

                    // Toggle filename, filesize and date
                    if (group == 'fds-setting') {
                        THEME.toggleFileNameSizeDate(setting);
                        // Toggle filename, filesize and date on datatable
                        if (ACTIVE_SETTINGS.view == 'list') {
                            THEME.toggleColumns();
                        }
                    }

                    // Change layput when sortby & orderby
                    if (setting == 'sortby' || setting == 'orderby') {
                        if (ACTIVE_SETTINGS.view == 'list') {
                            THEME.datatable.order([THEME.indexesColumn[ACTIVE_SETTINGS.sortby], ACTIVE_SETTINGS.orderby]).draw();
                        } else {
                            showImages();
                        }
                    }

                    // Change layout when thumbsize changed
                    if (setting == 'thumbsize') {
                        $($(this).data('target')).val(value);
                        THEME.updateThumbnail(value);
                    }

                    // Change layout when display type changed
                    if (setting == 'view') {
                        if (value == 'compact') {
                            // disableButton({attr: 'data-group', value: 'fds-setting'});
                            disableButton(['data-group', 'fds-setting']);
                            disableButton(['data-group', 'thumbsize-setting']);
                        } else if (value == 'list') {
                            disableButton(['data-group', 'thumbsize-setting']);
                            enableButton(['data-group', 'fds-setting']);
                            disableButton(['data-setting', 'filename']);
                        } else {
                            enableButton(['data-group', 'fds-setting']);
                            enableButton(['data-group', 'thumbsize-setting']);
                        }
                        showImages();
                    }
                });
            });
        });

        // Automatically loading images when on reload page
        if ($('.sidebar').find('.folder-selected').hasClass('folder-selected')) {
            $('.sidebar').find('.folder-selected .folder').click();
        }

        // Change sortby and orderby in settings modal when sorting header of datatable changed
        $(document).on('order.dt', '#datatable', function () {
            // Only handling when it is list type
            if (!THEME.datatable || ACTIVE_SETTINGS.view != 'list') {
                return;
            }
            // Current column for sorting
            let order = THEME.datatable.order();
            if (order.length > 0) {
                let indexColumn = order[0][0];
                let orderby = order[0][1];
                let columnSelected = THEME.getColumnByIndex(indexColumn);

                // Update sortby and orderby
                ACTIVE_SETTINGS.sortby  = columnSelected;
                ACTIVE_SETTINGS.orderby = orderby.toLowerCase();

                // Update SortBy and OrderBy in settings modal
                $(`select[data-setting="sortby"] option[value="${columnSelected}"]`).prop('selected', true);
                $(`input[data-setting="orderby"][value="${ACTIVE_SETTINGS.orderby}"]`).prop('checked', true);
            }
        });

        // window.opener.CKEDITOR.instances.editor.openDialog('mypluginDialog');
        // CKEDITOR.instances.editor.commands.insertMyPlugin.exec();
    });
</script>
</body>
</html>
