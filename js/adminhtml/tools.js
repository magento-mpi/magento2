function setLocation(url){
    window.location.href = url;
}

function deleteConfirm(message, url) {
    if( confirm(message) ) {
        setLocation(url);
    }
    return false;
}

function setElementDisable(element, disable){
    if($(element)){
        $(element).disabled = disable;
    }
}

function imagePreview(element){
    Effect.SlideDown(element);
    if(!$(element).observerAdded){
        Event.observe(element,'click',function(event){
            Effect.SlideUp(Event.element(event).parentNode);
        });
    }
    $(element).observerAdded = true;
}