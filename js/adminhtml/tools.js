function setLocation(url){
    window.location.href = url;
}

function deleteConfirm(message, url) {
    if( confirm(message) ) {
        setLocation(url);
    }
    return false;
}