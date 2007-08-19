function popWin(url,win,para) { window.open(url,win,para); }

function setLocation(url){
    window.location.href = url;
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
        
        if(headRows.length) headRows[headRows.length-1].addClassName('last');
        if(bodyRows.length) bodyRows[bodyRows.length-1].addClassName('last');
        if(footRows.length) footRows[footRows.length-1].addClassName('last');
        
        allRows.each(function(row){
            var cols = row.getElementsByTagName('TD');
            if(cols.length) {
                cols[cols.length-1].addClassName('last');
            };
        });
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


Varien.CompareController = Class.create();

Varien.CompareController.prototype = {
    initialize: function(container, options) {
        // Default configuration values
        this.updateUrl = false;
        this.removeUrl = false;
        this.successMessage = false;
        this.removeMessage = false;
        this.confirmMessage = false;
        this.useAjax = true;
        this.container = $(container);
        if(options) {
            $H(options).each(function(pair) {
                if(typeof this[pair.key] != 'function' && pair.key != 'container') {
                    if(pair.key == 'updateUrl' || pair.key == 'removeUrl') {
                        this[pair.key] = new Template(pair.value);
                    } else {
                        this[pair.key] = pair.value;
                    }
                }
            }.bind(this));
        }
    },
    addItem: function(id) {
        if(this.useAjax && this.container) {
            new Ajax.Updater(this.container, this.updateUrl.evaluate({id:id}) + '?ajax=1', {
                onComplete: function() {
                    if(this.successMessage) {
                        window.alert(this.successMessage);
                    }
                }.bind(this)
            });

            
        } else {
            window.location.href = this.updateUrl.evaluate({id:id});
        }
        
    },
    removeItem: function () {
        var item = arguments[0];
        var showMess = true;
        if(arguments[1]===false) {
            showMess = false;
        }
        if(!this.confirmMessage || !showMess || window.confirm(this.confirmMessage)) {
            
            var id = 0;
            var parentItem = false
            if(typeof item == 'object') {
                item    = $(item);
                parentItem = $(item.parentNode);
                if(parentItem.hasClassName('block-compare-item')) {
                    id = parentItem.getElementsByClassName('compare-item-id')[0].value;
                    parentItem.remove();
                    if(this.container.getElementsByClassName('block-compare-item').length == 0) {
                        
                    } else {
                        var items = this.container.getElementsByClassName('block-compare-item');
                        var lastItem = $(items[items.length-1]);
                        if(!lastItem.hasClassName('last')) {
                            lastItem.addClassName('last');
                        }
                    }
                } else {
                    return;
                }
            } else {
                id = item;
            }
            
            if(this.useAjax) {
                var removeMessage = this.removeMessage;
                var container = this.container;
                new Ajax.Updater(this.container, this.removeUrl.evaluate({id:id}) + '?ajax=1', {});
            } else {
                window.location.href = this.removeUrl.evaluate({id:id});
            }
        }
    },
    removeAll: function() {
        if(!this.confirmMessage || window.confirm(this.confirmMessage)) {
            new Ajax.Updater(this.container, this.removeUrl.evaluate({}) + '?ajax=1&all=1', {});
        }
    },
	openCompare: function(link) {
		this.compareWindow = window.open(link,'compareWindow','resizable=yes,width=800,height=600');
		this.compareWindow.focus();
	}
}