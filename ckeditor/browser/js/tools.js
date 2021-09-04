'use strict';

// Crop image
var $container = $('.container-app');
var cropBox = $('.crop-box');
var event_state = {};
var constraint  = false;
var crop_box_min_width  = 60;  // Change as required
var crop_box_min_height = 60;
var crop_box_max_width  = 800; // Change as required
var crop_box_max_height = 500;

var canvas, ctx;
var isLoadedImage = false;
var degrees = 0;
var curImgHeight = 0;
var curImgWidth  = 0;
var srcImageSelected = 'path/to/image';
var img = new Image();
img.crossOrigin = "Anonymous";

canvas = document.getElementById('canvas');
ctx = canvas.getContext("2d");
// Waiting image until it is loaded
img.onload = function () {
    isLoadedImage = true;
    curImgWidth   = img.height;
    curImgHeight  = img.width;
    canvas.width  = curImgWidth;
    canvas.height = curImgHeight;
    // Show image
    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
};
img.src = srcImageSelected;

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

// Start event for corners (8) of crop box
cropBox.on('mousedown touchstart', '.crop-handle', function (e) {
    e.preventDefault();
    e.stopPropagation();

    saveEventState(e);

    // When press and holder a corner of crop box via mouse
    $container.on('mousemove touchmove', resizing);
    // When release the mouse
    $container.on('mouseup touchend', endResize);
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
    $container.off('mouseup touchend', endResize);
    $container.off('mousemove touchmove', resizing);
    // Turn on draggling crop box when finished pulling its corners
    $container.find('.crop-box').draggable({disable: false});
}

function cropImage() {
    var left   = cropBox.offset().left - $(canvas).offset().left;
    var top    = cropBox.offset().top  - $(canvas).offset().top;
    var width  = cropBox.width();
    var height = cropBox.height();

    ctx.drawImage(img, left, top, width, height, 0, 0, width, height);
}

function rotateClockWise() {
    degrees += 90 % 360;
    rotateImage(degrees);
    // Prevent image cliped when rotating over 4 times
    if (degrees == 360) {
        degrees = 0;
    }
}

function rotateCounterClockWise() {
    if (degrees == 0) {
        degrees = 270;
    } else {
        degrees -= 90;
    }
    rotateImage(degrees);
}

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
}

function resizeImage(width, height) {
    canvas.width  = width || curImgWidth;
    canvas.height = height || curImgHeight;
    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
    ctx.save();
}