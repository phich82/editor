<html>
<head>
  <meta charset="utf-8">
  <link rel="shortcut icon" href="./favicon.ico" type="image/x-icon">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="./ckeditor/ckeditor.js"></script>
  <style>
    #wrap-editor {
      position: relative;
    }
    #editor {
      border: 1px solid grey;
      width: 100%;
      height: 100%;
    }
    #save {
      position: absolute;
      top: 2px;
      right: 2px;
      background-color: blue;
      border: none;
      color: white;
      padding: 5px;
      cursor: pointer;
      z-index: 2;
    }
    #wrap-file {
      margin-bottom: 10px;
    }
    #wrap-file .label {
      margin-bottom: 5px;
    }
  </style>
</head>
<body>
  <div id="wrap-file">
    <div class="label">Choose a templete</div>
    <input type="file" id="file" />
  </div>
  <div id="wrap-editor">
    <button id="save">Save</button>
    <div id="editor"></div>
  </div>

<script>
var Session = {
  set(key, value) {
    localStorage.setItem(key, value);
  },
  get(key) {
    if (localStorage.hasOwnProperty(key)) {
      return localStorage.getItem(key);
    }
    return false;
  }
};

var Editor = {
  instance(editor) {
    return CKEDITOR.instances[editor || 'editor'];
  },
  updateData(data) {
    this.instance().setData(data || Session.get('file'));
  },
  getData() {
    return this.instance().getData.getData();
  },
  setSize(height, width) {
    if (height !== undefined) {
      this.instance().config.height = height;
    }
    if (width !== undefined) {
      this.instance().config.width = width;
    }
  }
};

$(function() {
  // Initiate CKEDITOR
  CKEDITOR.replace('editor', {
    extraPlugins: 'filebrowser,imagecrop',
    filebrowserUploadMethod: 'form',

    filebrowserBrowseUrl: '/ckeditor/browser/browse.php?type=Files',
    filebrowserImageBrowseUrl: '/ckeditor/browser/browse.php?type=Images',
    filebrowserFlashBrowseUrl: '/ckeditor/browser/browse.php?type=Flash',

    filebrowserUploadUrl: '/ckeditor/uploader/upload.php?type=Files',
    filebrowserImageUploadUrl: '/ckeditor/uploader/upload.php?type=Images',
    filebrowserFlashUploadUrl: '/ckeditor/uploader/upload.php?type=Flash',

    // filebrowserWindowWidth: '640',
    // filebrowserWindowHeight: '480',
    image_previewText: ' ',

    // image-crop plugin: https://github.com/anndoc/image-crop
    allowedContent: true,
    // toolbar: "Custom",
    // toolbar_Custom: [{'name': 'insert', 'items': ['ImageCrop']}],
    // Setup cropper options. (See cropper.js documentation https://github.com/fengyuanchen/cropperjs)
    cropperOption: {
      // "aspectRatio": 1.8,
      // "autoCropArea": 1,
      background: false,
      cropBoxResizable: true,// false,
      dragMode: 'move'
    },
    // Add js and css urls to cropper.js
    // cropperJsUrl: "https://cdnjs.cloudflare.com/ajax/libs/cropperjs/0.8.1/cropper.min.js",
    // cropperCssUrl: "https://cdnjs.cloudflare.com/ajax/libs/cropperjs/0.8.1/cropper.min.css",
    cropperJsUrl: "https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js",
    cropperCssUrl: "https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css",
  });

  // Setup size of editor
  Editor.setSize('100%', '100%');

  // Load the template if cached
  if (Session.get('file')) {
    Editor.updateData();
  }

  // Change template
  $('#file').on('change', function() {
    var reader = new FileReader();
    reader.onload = function() {
      Session.set('file', reader.result);
      Editor.updateData();
    }
    reader.readAsText(this.files[0]);
  });

  // Save template
  $('#save').click(function () {
    Session.set('file', CKEDITOR.instances.editor.getData());
    Editor.updateData();
  });
});
</script>
</body>
</html>


