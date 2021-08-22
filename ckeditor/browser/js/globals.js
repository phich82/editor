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
