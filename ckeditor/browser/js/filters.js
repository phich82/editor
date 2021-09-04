
function brightness(brightnessValue, canvas, ctx) {
    var imgX = 0;
    var imgY = 0;
    ctx.drawImage(img, imgX, imgY, canvas.width, canvas.height);
    var scannedImg  = ctx.getImageData(imgX, imgY, canvas.width, canvas.height);
    var scannedData = scannedImg.data; // raw pixel data in array
    // Brightness multiplier
    var brightnessMul = 1 + brightnessValue / 100;
    for (var i = 0; i < scannedData.length; i += 4) {
        var r = scannedData[i];     // Extract original red color [0 to 255]
        var g = scannedData[i + 1]; // Extract green
        var b = scannedData[i + 2]; // Extract blue
        /**
         * Should make sure the values (Red, Green, and Blue) are between 0 and 255.
         * Using Math.max(0, Math.min(255, brightenedRed))
         */
        scannedData[i]   = Math.max(0, Math.min(255, brightnessMul * r));
        scannedData[i+1] = Math.max(0, Math.min(255, brightnessMul * g));
        scannedData[i+2] = Math.max(0, Math.min(255, brightnessMul * b));
    }
    scannedImg.data = scannedData;
    ctx.putImageData(scannedImg, imgX, imgY);
}

function contrast(contrastValue, canvas, ctx) {
    var imgX = 0;
    var imgY = 0;
    ctx.drawImage(img, imgX, imgY, canvas.width, canvas.height);
    var scannedImg  = ctx.getImageData(imgX, imgY, canvas.width, canvas.height);
    var scannedData = scannedImg.data; // raw pixel data in array
    var vContrast   = 1 + contrastValue / 100;
    if (vContrast > 0) {
        for (var i = 0; i < scannedData.length; i += 4) {
            scannedData[i]   += (255 - scannedData[i])   * vContrast / 255; // red
            scannedData[i+1] += (255 - scannedData[i+1]) * vContrast / 255; // green
            scannedData[i+2] += (255 - scannedData[i+2]) * vContrast / 255; // blue
        }
    } else if (vContrast < 0) {
        for (var i = 0; i < scannedData.length; i += 4) {
            scannedData[i]   += scannedData[i]   * (vContrast) / 255; // red
            scannedData[i+1] += scannedData[i+1] * (vContrast) / 255; // green
            scannedData[i+2] += scannedData[i+2] * (vContrast) / 255; // blue
        }
    }
    scannedImg.data = scannedData;
    ctx.putImageData(scannedImg, imgX, imgY);
}

/**
 * 
 * @param {*} ctx 
 * @param {*} w 
 * @param {*} h 
 * @param numberic mix [0-1]
 */
function sharpen(ctx, w, h, mix) {
    var x, sx, sy, r, g, b, a, dstOff, srcOff, wt, cx, cy, scy, scx,
        weights = [0, -1, 0, -1, 5, -1, 0, -1, 0],
        katet = Math.round(Math.sqrt(weights.length)),
        half = (katet * 0.5) | 0,
        dstData = ctx.createImageData(w, h),
        dstBuff = dstData.data,
        srcBuff = ctx.getImageData(0, 0, w, h).data,
        y = h;

    while (y--) {
        x = w;
        while (x--) {
            sy = y;
            sx = x;
            dstOff = (y * w + x) * 4;
            r = 0;
            g = 0;
            b = 0;
            a = 0;

            for (cy = 0; cy < katet; cy++) {
                for (cx = 0; cx < katet; cx++) {
                    scy = sy + cy - half;
                    scx = sx + cx - half;

                    if (scy >= 0 && scy < h && scx >= 0 && scx < w) {
                        srcOff = (scy * w + scx) * 4;
                        wt = weights[cy * katet + cx];

                        r += srcBuff[srcOff] * wt;
                        g += srcBuff[srcOff + 1] * wt;
                        b += srcBuff[srcOff + 2] * wt;
                        a += srcBuff[srcOff + 3] * wt;
                    }
                }
            }

            dstBuff[dstOff] = r * mix + srcBuff[dstOff] * (1 - mix);
            dstBuff[dstOff + 1] = g * mix + srcBuff[dstOff + 1] * (1 - mix);
            dstBuff[dstOff + 2] = b * mix + srcBuff[dstOff + 2] * (1 - mix);
            dstBuff[dstOff + 3] = srcBuff[dstOff + 3];
        }
    }

    ctx.putImageData(dstData, 0, 0);
}

function applyBrightness(data, brightness) {
    for (var i = 0; i < data.length; i+= 4) {
        data[i]   += 255 * (brightness / 100);
        data[i+1] += 255 * (brightness / 100);
        data[i+2] += 255 * (brightness / 100);
    }
}

function applyContrast(data, contrast) {
    var factor = (259.0 * (contrast + 255.0)) / (255.0 * (259.0 - contrast));
    for (var i = 0; i < data.length; i+= 4) {
      data[i]   = Math.max(0, Math.min(255, (factor * (data[i]   - 128.0) + 128.0)));
      data[i+1] = Math.max(0, Math.min(255, (factor * (data[i+1] - 128.0) + 128.0)));
      data[i+2] = Math.max(0, Math.min(255, (factor * (data[i+2] - 128.0) + 128.0)));
    }
}
