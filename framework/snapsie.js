function isQuirksMode(inDocument) {
    return (inDocument.compatMode == 'BackCompat');
}

function getDrawableElement(inDocument) {
    if (isQuirksMode(inDocument)) {
        return inDocument.getElementsByTagName('body')[0];
    }
    else {
        return inDocument.documentElement;
    }
}

/**
 * Returns the canonical Windows path for a given path. This means
 * basically replacing any forwards slashes with backslashes.
 *
 * @param path  the path whose canonical form to return
 */
function getCanonicalPath(path) {
    path = path.replace(/\//g, '\\');
    path = path.replace(/\\\\/g, '\\');
    return path;
}

/**
 * Saves a screenshot of the current document to a file. If frameId is
 * specified, a screenshot of just the frame is captured instead.
 *
 * @param outputFile  the file to which to save the screenshot
 * @param frameId     the frame to capture; omit to capture entire document
 */
function saveSnapshot(outputFile, frameId) {
    try {
        var nativeObj = new ActiveXObject('Snapsie.CoSnapsie');
    }
    catch (e) {return}

    var drawableElement = getDrawableElement(document);
    var drawableInfo = {
        overflow  : drawableElement.style.overflow
        , scrollLeft: drawableElement.scrollLeft
        , scrollTop : drawableElement.scrollTop
    };
    drawableElement.style.overflow = 'hidden';

    var capturableDocument;
    var frameBCR = { left: 0, top: 0 };
    if (!frameId) {
        capturableDocument = document;
    }
    else {
        var frame = document.getElementById(frameId);
        capturableDocument = frame.document;

        // scroll as much of the frame into view as possible
        frameBCR = frame.getBoundingClientRect();
        window.scroll(frameBCR.left, frameBCR.top);
        frameBCR = frame.getBoundingClientRect();
    }
    try {
        nativeObj.saveSnapshot(
            getCanonicalPath(outputFile),
            frameId,
            drawableElement.scrollWidth,
            drawableElement.scrollHeight,
            drawableElement.clientWidth,
            drawableElement.clientHeight,
            drawableElement.clientLeft,
            drawableElement.clientTop,
            frameBCR.left,
            frameBCR.top
        );
    }
    catch (e) {}

    // revert
    drawableElement.style.overflow = drawableInfo.overflow;
    drawableElement.scrollLeft = drawableInfo.scrollLeft;
    drawableElement.scrollTop = drawableInfo.scrollTop;
}
saveSnapshot('%filePath%');

