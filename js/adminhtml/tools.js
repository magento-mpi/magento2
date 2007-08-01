function setLocation(url){
    window.location.href = url;
}

function confirmSetLocation(message, url){
    if( confirm(message) ) {
        setLocation(url);
    }
    return false;
}

function deleteConfirm(message, url) {
    confirmSetLocation(message, url);
}

function setElementDisable(element, disable){
    if($(element)){
        $(element).disabled = disable;
    }
}

slidedDown = false;
function imagePreview(element){
    if(!$(element).observerAdded){
        if(slidedDown){
            Effect.SlideUp(slidedDown);
            $(slidedDown).observerAdded = false;
        }
        Effect.SlideDown(element);
        Event.observe(element,'click',function(event){
            Effect.SlideUp(Event.element(event).parentNode);
            slidedDown = false;
            $(element).observerAdded = false;
        });
        slidedDown = element;
        $(element).observerAdded = true;
    }
    else{
        Effect.SlideUp(element);
        slidedDown = false;
        $(element).observerAdded = false;
    }
}

/********** MESSAGES ***********/
Event.observe(window, 'load', function() {
    $$('.messages .error-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E13422', endcolor:'#fdf9f8', duration:1});
    });
    $$('.messages .warning-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E13422', endcolor:'#fdf9f8', duration:1});
    });
    $$('.messages .notice-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#E5B82C', endcolor:'#fbf7e9', duration:1});
    });
    $$('.messages .success-msg').each(function(el) {
        new Effect.Highlight(el, {startcolor:'#507477', endcolor:'#f2fafb', duration:1});
    });
});
