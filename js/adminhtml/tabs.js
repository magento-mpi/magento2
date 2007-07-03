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
                    tabContentElement.tabsObject = this;
                    tabContentElement.tabObject  = this.tabs[tab];
                    tabContentElement.changeRelation = this.tabs[tab];
                }
            }
        }
        this.showTabContent($(activeTabId));
    },
    
    getTabContentElementId : function(tab){
        return tab.id+'_content';
    },
    
    tabMouseClick : function(event){
        var tab = Event.findElement(event, 'a');
        if(tab.href.indexOf('#') != tab.href.length-1){
            alert(tab.href);
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