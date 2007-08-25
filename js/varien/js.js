function popWin(url,win,para) { window.open(url,win,para); }

function setLocation(url){
    window.location.href = url;
}

function setPLocation(url, setFocus){
    if( setFocus ) {
        window.opener.focus();
    }
    window.opener.location.href = url;
}


function decorateTable(table){
    if($(table)){
        var allRows = $(table).getElementsBySelector('tr')
        var bodyRows = $(table).getElementsBySelector('tbody tr');
        var headRows = $(table).getElementsBySelector('thead tr');
        var footRows = $(table).getElementsBySelector('tfoot tr');

        for(var i=0; i<bodyRows.length; i++){
            if((i+1)%2==0) {
                bodyRows[i].addClassName('even');
            }
            else {
                bodyRows[i].addClassName('odd');
            }
        }
        
        if(headRows.length) {
        	headRows[0].addClassName('first');
        	headRows[headRows.length-1].addClassName('last');
        }
        if(bodyRows.length) {
        	bodyRows[0].addClassName('first');
        	bodyRows[bodyRows.length-1].addClassName('last');
        }
        if(footRows.length) {
        	footRows[0].addClassName('first');
        	footRows[footRows.length-1].addClassName('last');
        }
        if(allRows.length) {
            for(var i=0;i<allRows.length;i++){
                var cols =allRows[i].getElementsByTagName('TD');
                if(cols.length) {
                    Element.addClassName(cols[cols.length-1], 'last');
                };
            }
        }
    }
}

function decorateList(list){
    if($(list)){
        var items = $(list).getElementsBySelector('li')
        if(items.length) items[items.length-1].addClassName('last');
        for(var i=0; i<items.length; i++){
            if((i+1)%2==0) 
                items[i].addClassName('even');
            else
                items[i].addClassName('odd');
        }
    }
}

function decorateDataList(list){
	list = $(list);
    if(list){
        var items = list.getElementsBySelector('dt')
        if(items.length) items[items.length-1].addClassName('last');
        for(var i=0; i<items.length; i++){
            if((i+1)%2==0) 
                items[i].addClassName('even');
            else
                items[i].addClassName('odd');
        }
        var items = list.getElementsBySelector('dd')
        if(items.length) items[items.length-1].addClassName('last');
        for(var i=0; i<items.length; i++){
            if((i+1)%2==0) 
                items[i].addClassName('even');
            else
                items[i].addClassName('odd');
        }
    }
}


// Version 1.0
var isIE = navigator.appVersion.match(/MSIE/) == "MSIE";

if (!window.Varien)
    var Varien = new Object();

Varien.showLoading = function(){
    Element.show('loading-process');
}
Varien.hideLoading = function(){
    Element.hide('loading-process');
}
Varien.GlobalHandlers = {
    onCreate: function() {
        Varien.showLoading();
    },

    onComplete: function() {
        if(Ajax.activeRequestCount == 0) {
            Varien.hideLoading();
        }
    }
};

Ajax.Responders.register(Varien.GlobalHandlers);