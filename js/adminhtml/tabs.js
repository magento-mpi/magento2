var varienTabs = new Class.create();

varienTabs.prototype = {
    initialize : function(containerId, destElementId,  activeTabId){
        this.containerId    = containerId;
        this.destElementId  = destElementId;
        this.activeTab = null;
        
        this.tabOnClick     = this.tabMouseClick.bindAsEventListener(this);
        
        this.tabs = $$('#'+this.containerId+' li a.tab-item-link');
        
        this.hideAllTabsContent();
        for(var tab in this.tabs){
            Event.observe(this.tabs[tab],'click',this.tabOnClick);
            // move tab contents to destination element
            if($(this.destElementId)){
                var tabContentElement = $(this.getTabContentElementId(this.tabs[tab]));
                if(tabContentElement && tabContentElement.parentNode.id != this.destElementId){
                    $(this.destElementId).appendChild(tabContentElement);
                    tabContentElement.container = this;
                    tabContentElement.statusBar = this.tabs[tab];
                    tabContentElement.tabObject  = this.tabs[tab];
                }
            }
        }
        this.showTabContent($(activeTabId));
        //Event.observe(window,'load',this.moveTabContentInDest.bind(this));
    },
    
    moveTabContentInDest : function(){
        for(var tab in this.tabs){
            if($(this.destElementId)){
                var tabContentElement = $(this.getTabContentElementId(this.tabs[tab]));
                if(tabContentElement && tabContentElement.parentNode.id != this.destElementId){
                    $(this.destElementId).appendChild(tabContentElement);
                    tabContentElement.container = this;
                    tabContentElement.statusBar = this.tabs[tab];
                    tabContentElement.tabObject  = this.tabs[tab];
                }
            }
        }
    },
    
    getTabContentElementId : function(tab){
        if(tab){
            return tab.id+'_content';
        }
        return false;
    },
    
    tabMouseClick : function(event){
        var tab = Event.findElement(event, 'a');
        if(tab.href.indexOf('#') != tab.href.length-1){
            if(Element.hasClassName(tab, 'ajax')){
                
            }
            else{
                location.href = tab.href;
            }
        }
        else {
            this.showTabContent(tab);
        }
        
        Event.stop(event);
    },
    
    hideAllTabsContent : function(){
        for(var tab in this.tabs){
            this.hideTabContent(this.tabs[tab]);
        }
    },
    
    showTabContent : function(tab){
        this.hideAllTabsContent();
        var tabContentElement = $(this.getTabContentElementId(tab));
        if(tabContentElement){
            Element.show(tabContentElement);
            //new Effect.Appear(tabContentElement, {duration :0.3});
            Element.addClassName(tab, 'active');
            this.activeTab = tab;
        }
        if(varienGlobalEvents){
            varienGlobalEvents.fireEvent('showTab', {tab:tab});
        }
    },
    
    hideTabContent : function(tab){
        var tabContentElement = $(this.getTabContentElementId(tab));
        if($(this.destElementId) && tabContentElement){
           Element.hide(tabContentElement);
           Element.removeClassName(tab, 'active');
        }
        if(varienGlobalEvents){
            varienGlobalEvents.fireEvent('hideTab', {tab:tab});
        }
    }
}