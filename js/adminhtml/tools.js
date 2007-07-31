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

function imagePreview(element){
    Effect.SlideDown(element);
    if(!$(element).observerAdded){
        Event.observe(element,'click',function(event){
            Effect.SlideUp(Event.element(event).parentNode);
        });
    }
    $(element).observerAdded = true;
}

/********** MESSAGES ***********/
Event.observe(window, 'load', function() {
    $$('.messages .error-msg').each(function(li) {
        new Effect.Highlight(li, {startcolor:'#E13422', endcolor:'#fdf9f8', duration:1});
    });
    $$('.messages .warning-msg').each(function(li) {
        new Effect.Highlight(li, {startcolor:'#E13422', endcolor:'#fdf9f8', duration:1});
    });
    $$('.messages .notice-msg').each(function(li) {
        new Effect.Highlight(li, {startcolor:'#E5B82C', endcolor:'#fbf7e9', duration:1});
    });
    $$('.messages .success-msg').each(function(li) {
        new Effect.Highlight(li, {startcolor:'#507477', endcolor:'#f2fafb', duration:1});
    });
});