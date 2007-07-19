function deleteConfirm(message, url) {
    if( confirm(message) ) {
        window.location.href = url;
    }
    return false;
}