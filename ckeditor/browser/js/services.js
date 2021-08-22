var Service = {};

Service.download = function (url, fileName) {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, true);
    xhr.responseType = "blob";
    xhr.onload = function() {
        var urlCreator = window.URL || window.webkitURL;
        var imageUrl = urlCreator.createObjectURL(this.response);
        var a = document.createElement('a');
        a.href = imageUrl;
        a.download = fileName || url.split('/').pop();
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };
    xhr.send();
};

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
    view: function(callback) {
        if (typeof callback !== 'function') {
            callback = function () {};
        }
        params.action = 'view';
        $.ajax({
            url: '../uploader/do_file.php',
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
            url: '../uploader/do_file.php',
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
            url: '../uploader/do_file.php',
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
            url: '../uploader/do_file.php',
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
            url: '../uploader/do_file.php',
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