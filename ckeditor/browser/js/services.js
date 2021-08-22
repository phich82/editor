var Service = {};

Service.Folder = {
    create: function(callback) {
        if (typeof callback === 'function') {
            callback();
        }
    },
    rename: function(callback) {
        if (typeof callback === 'function') {
            callback();
        }
    },
    delete: function(callback) {
        if (typeof callback === 'function') {
            callback();
        }
    },
};

Service.Image = {
    apply: function(callback) {
        if (typeof callback !== 'function') {
            callback = function () {};
        }
    },
    view: function(callback) {
        if (typeof callback !== 'function') {
            callback = function () {};
        }
        params.action = 'view';
        $.ajax({
            url: '../uploader/get_files.php',
            method: 'POST',
            data: params,
            dataType: 'json',
            success: function (response) {
                let data = response.success ? response.data : response.error;
                callback(response.success, data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                callback(false, jqXHR.responseText, jqXHR);
            }
        });
    },
    download: function(params, callback) {
        if (typeof callback !== 'function') {
            callback = function () {};
        }
        params.action = 'download';
        $.ajax({
            url: '../uploader/get_files.php',
            method: 'POST',
            data: params,
            dataType: 'json',
            success: function (response) {
                let data = response.success ? response.data : response.error;
                callback(response.success, data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                callback(false, jqXHR.responseText, jqXHR);
            }
        });
    },
    edit: function(callback) {
        if (typeof callback !== 'function') {
            callback = function () {};
        }
        params.action = 'edit';
        $.ajax({
            url: '../uploader/get_files.php',
            method: 'POST',
            data: params,
            dataType: 'json',
            success: function (response) {
                let data = response.success ? response.data : response.error;
                callback(response.success, data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                callback(false, jqXHR.responseText, jqXHR);
            }
        });
    },
    rename: function(callback) {
        if (typeof callback !== 'function') {
            callback = function () {};
        }
        params.action = 'rename';
        $.ajax({
            url: '../uploader/get_files.php',
            method: 'POST',
            data: params,
            dataType: 'json',
            success: function (response) {
                let data = response.success ? response.data : response.error;
                callback(response.success, data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                callback(false, jqXHR.responseText, jqXHR);
            }
        });
    },
    delete: function(callback) {
        if (typeof callback !== 'function') {
            callback = function () {};
        }
        params.action = 'delete';
        $.ajax({
            url: '../uploader/get_files.php',
            method: 'POST',
            data: params,
            dataType: 'json',
            success: function (response) {
                let data = response.success ? response.data : response.error;
                callback(response.success, data);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                callback(false, jqXHR.responseText, jqXHR);
            }
        });
    }
};