// Helper function to get parameters from the query string.
function getUrlParam(paramName) {
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

// Convert base64 to blob data
function base64ToBlob(base64, mime) {
    base64 = base64.replace(/^data:image\/(.*);base64,/, '');
    mime = mime || '';

    var sliceSize = 1024;
    var byteChars = window.atob(base64);
    var byteArrays = [];

    for (var offset = 0, len = byteChars.length; offset < len; offset += sliceSize) {
        var slice = byteChars.slice(offset, offset + sliceSize);

        var byteNumbers = new Array(slice.length);
        for (var i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i);
        }

        var byteArray = new Uint8Array(byteNumbers);

        byteArrays.push(byteArray);
    }

    return new Blob(byteArrays, {type: mime});
}

// Format filesize
function format_filesize(size, decimals) {
    decimals = decimals || 2;
    var i = Math.floor(Math.log(size) / Math.log(1024));
    return (size / Math.pow(1024, i)).toFixed(decimals) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i];
};

// Hide element after the specified time
function hideAfter(element, time) {
    setTimeout(function () {
        if (typeof element !== 'object') {
            return $(element).html('').hide();
        }
        return element.html('').hide();
    }, time || 5000);
}

// Get filename from path
function getFileName(path) {
    if (!path) {
        return false;
    }
    return path.split('/').pop();
}

// Get base path  from path
function basepath(path) {
    if (!path) {
        return false;
    }
    let parts = path.split('/');
    let filename = parts.pop();
    if (filename.split('.').length > 1) {
        return parts.join('/');
    }
    return path;
}

function createWrapper(callback, className) {
    callback = typeof callback === 'function' ? callback : function () {};
    className = className || 'wrap-modal-app';
    $('body').append(`
        <div class="wrap-modal-app"
             style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 2000;"
        ></div>
    `);
    callback(className);
}

function load(pathFile, callback, classNameWrap) {
    createWrapper(function (_classNameWrap) {
        $('body').find(`.${_classNameWrap}`).load(pathFile, function () {
            callback(_classNameWrap);
        });
    }, classNameWrap);
}

function debounce (func, delay) {
    let timer;
    delay = delay || 300;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timer);
        timer = setTimeout(function () {
            func.apply(context, args);
        }, delay);
    }

    // let timer;
    // return funcition () {
    //     const context = this;
    //     if (!timer) {
    //         func.apply(context, args);
    //     }
    //     clearTimeout(timer);
    //     timer = setTimeout(function () {
    //         timer = undefined;
    //     }, delay);
    // };
}

function download(canvas, filename, mime, quality) {
    mime = mime || 'image/jpeg';
    quality = quality || 0.92; // between 0 and 1
    const link = document.createElement('a');
    link.download = filename;
    link.href = canvas.toDataURL(mime, quality);
    // Dispatch click event
    link.dispatchEvent(new MouseEvent('click'));
}

function isFullScreen() {
    return document.fullScreenElement ||
           document.mozFullScreen ||
           document.webkitIsFullScreen;
}

function openFullScreen(elem) {
    if (isFullScreen()) {
        return;
    }
    var w = elem || document.documentElement; // <html>
    var fullscreen = w.requestFullscreen ||
                     w.webkitRequestFullscreen || // Safari
                     w.mozRequestFullScreen ||
                     w.msRequestFullscreen; // IE11
    if (fullscreen) {
        fullscreen.call(w);
    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript !== null) {
            wscript.SendKeys("{F11}");
        }
    }
}

function closeFullScreen() {
    if (!isFullScreen()) {
        return;
    }
    var w = document;
    var exitFullScreen = w.exitFullscreen ||
                         w.webkitExitFullscreen || // Safari
                         w.msExitFullscreen || // IE11
                         w.cancelFullScreen ||
                         w.webkitCancelFullScreen ||
                         w.mozCancelFullScreen;
    if (typeof exitFullScreen === 'function') {
        exitFullScreen.call(w);
    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript !== null) {
            wscript.SendKeys("{F11}");
        }
    }
}
