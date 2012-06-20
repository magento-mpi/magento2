window.oldSetTimeout = window.setTimeout;
window.setTimeout = function(func, delay){
     window.oldSetTimeout(function() {
        try {
            window.onbeforeunload = function (e) {
                var e = e || window.event;
                if (e) {
                    e.returnValue = 'Are you sure, you want to leave this page?';
                }
                window.oldSetTimeout(function(){window.onbeforeunload = null;}, 0);
                return 'Are you sure, you want to leave this page?';
            };
            func();
            window.oldSetTimeout(function(){window.onbeforeunload = null;}, 0);
        }
        catch (exception) {
        }
    }, delay);
};