/*
 * Ext JS Library 1.0 Beta 2
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.menu.Menu=function(config){Ext.apply(this,config);this.id=this.id||Ext.id();this.events={beforeshow:true,beforehide:true,show:true,hide:true,click:true,mouseover:true,mouseout:true,itemclick:true};Ext.menu.MenuMgr.register(this);var mis=this.items;this.items=new Ext.util.MixedCollection();if(mis){this.add.apply(this,mis);}};Ext.extend(Ext.menu.Menu,Ext.util.Observable,{minWidth:120,shadow:"sides",subMenuAlign:"tl-tr?",defaultAlign:"tl-bl?",allowOtherMenus:false,render:function(){if(this.el){return;}
var el=this.el=new Ext.Layer({cls:"x-menu",shadow:this.shadow,constrain:false,parentEl:this.parentEl||document.body,zindex:15000});this.keyNav=new Ext.menu.MenuNav(this);if(this.plain){el.addClass("x-menu-plain");}
if(this.cls){el.addClass(this.cls);}
this.focusEl=el.createChild({tag:"a",cls:"x-menu-focus",href:"#",onclick:"return false;",tabIndex:"-1"});var ul=el.createChild({tag:"ul",cls:"x-menu-list"});ul.on("click",this.onClick,this);ul.on("mouseover",this.onMouseOver,this);ul.on("mouseout",this.onMouseOut,this);this.items.each(function(item){var li=document.createElement("li");li.className="x-menu-list-item";ul.dom.appendChild(li);item.render(li,this);},this);this.ul=ul;this.autoWidth();},autoWidth:function(){var el=this.el,ul=this.ul;if(!el){return;}
var w=this.width;if(w){el.setWidth(w);}else if(Ext.isIE){el.setWidth(this.minWidth);var t=el.dom.offsetWidth;el.setWidth(ul.getWidth()+el.getFrameWidth("lr"));}},delayAutoWidth:function(){if(this.rendered){if(!this.awTask){this.awTask=new Ext.util.DelayedTask(this.autoWidth,this);}
this.awTask.delay(20);}},findTargetItem:function(e){var t=e.getTarget(".x-menu-list-item",this.ul,true);if(t&&t.menuItemId){return this.items.get(t.menuItemId);}},onClick:function(e){var t;if(t=this.findTargetItem(e)){t.onClick(e);this.fireEvent("click",this,t,e);}},setActiveItem:function(item,autoExpand){if(item!=this.activeItem){if(this.activeItem){this.activeItem.deactivate();}
this.activeItem=item;item.activate(autoExpand);}else if(autoExpand){item.expandMenu();}},tryActivate:function(start,step){var items=this.items;for(var i=start,len=items.length;i>=0&&i<len;i+=step){var item=items.get(i);if(!item.disabled&&item.canActivate){this.setActiveItem(item,false);return item;}}
return false;},onMouseOver:function(e){var t;if(t=this.findTargetItem(e)){if(t.canActivate&&!t.disabled){this.setActiveItem(t,true);}}
this.fireEvent("mouseover",this,e,t);},onMouseOut:function(e){var t;if(t=this.findTargetItem(e)){if(t==this.activeItem&&t.shouldDeactivate(e)){this.activeItem.deactivate();delete this.activeItem;}}
this.fireEvent("mouseout",this,e,t);},isVisible:function(){return this.el&&this.el.isVisible();},show:function(el,pos,parentMenu){this.parentMenu=parentMenu;if(!this.el){this.render();}
this.fireEvent("beforeshow",this);this.showAt(this.el.getAlignToXY(el,pos||this.defaultAlign),parentMenu,false);},showAt:function(xy,parentMenu,_fireBefore){this.parentMenu=parentMenu;if(!this.el){this.render();}
if(_fireBefore!==false){this.fireEvent("beforeshow",this);}
this.el.setXY(xy);this.el.show();this.focusEl.focus.defer(50,this.focusEl);this.fireEvent("show",this);},hide:function(deep){if(this.el&&this.isVisible()){this.fireEvent("beforehide",this);if(this.activeItem){this.activeItem.deactivate();this.activeItem=null;}
this.el.hide();this.fireEvent("hide",this);}
if(deep===true&&this.parentMenu){this.parentMenu.hide(true);}},add:function(){var a=arguments,l=a.length,item;for(var i=0;i<l;i++){var el=a[i];if(el.render){item=this.addItem(el);}else if(typeof el=="string"){if(el=="separator"||el=="-"){item=this.addSeparator();}else{item=this.addText(el);}}else if(el.tagName||el.el){item=this.addElement(el);}else if(typeof el=="object"){item=this.addMenuItem(el);}}
return item;},getEl:function(){if(!this.el){this.render();}
return this.el;},addSeparator:function(){return this.addItem(new Ext.menu.Separator());},addElement:function(el){return this.addItem(new Ext.menu.BaseItem(el));},addItem:function(item){this.items.add(item);if(this.ul){var li=document.createElement("li");li.className="x-menu-list-item";this.ul.dom.appendChild(li);item.render(li,this);this.delayAutoWidth();}
return item;},addMenuItem:function(config){if(!(config instanceof Ext.menu.Item)){config=new Ext.menu.Item(config);}
return this.addItem(config);},addText:function(text){return this.addItem(new Ext.menu.TextItem(text));},insert:function(index,item){this.items.insert(index,item);if(this.ul){var li=document.createElement("li");li.className="x-menu-list-item";this.ul.dom.insertBefore(li,this.ul.dom.childNodes[index]);item.render(li,this);this.delayAutoWidth();}
return item;},remove:function(item){this.items.removeKey(item.id);item.destroy();},removeAll:function(){var f;while(f=this.items.first()){this.remove(f);}}});Ext.menu.MenuNav=function(menu){Ext.menu.MenuNav.superclass.constructor.call(this,menu.el);this.scope=this.menu=menu;};Ext.extend(Ext.menu.MenuNav,Ext.KeyNav,{doRelay:function(e,h){var k=e.getKey();if(!this.menu.activeItem&&e.isNavKeyPress()&&k!=e.SPACE&&k!=e.RETURN){this.menu.tryActivate(0,1);return false;}
return h.call(this.scope||this,e,this.menu);},up:function(e,m){if(!m.tryActivate(m.items.indexOf(m.activeItem)-1,-1)){m.tryActivate(m.items.length-1,-1);}},down:function(e,m){if(!m.tryActivate(m.items.indexOf(m.activeItem)+1,1)){m.tryActivate(0,1);}},right:function(e,m){if(m.activeItem){m.activeItem.expandMenu(true);}},left:function(e,m){m.hide();if(m.parentMenu&&m.parentMenu.activeItem){m.parentMenu.activeItem.activate();}},enter:function(e,m){if(m.activeItem){e.stopPropagation();m.activeItem.onClick(e);m.fireEvent("click",this,m.activeItem);return true;}}});

Ext.menu.MenuMgr=function(){var menus,active,groups={};function init(){menus={},active=new Ext.util.MixedCollection();Ext.get(document).addKeyListener(27,function(){if(active.length>0){hideAll();}});}
function hideAll(){if(active.length>0){var c=active.clone();c.each(function(m){m.hide();});}}
function onHide(m){active.remove(m);if(active.length<1){Ext.get(document).un("mousedown",onMouseDown);}}
function onShow(m){var last=active.last();active.add(m);if(active.length==1){Ext.get(document).on("mousedown",onMouseDown);}
if(m.parentMenu){m.getEl().setZIndex(parseInt(m.parentMenu.getEl().getStyle("z-index"),10)+3);m.parentMenu.activeChild=m;}else if(last&&last.isVisible()){m.getEl().setZIndex(parseInt(last.getEl().getStyle("z-index"),10)+3);}}
function onBeforeHide(m){if(m.activeChild){m.activeChild.hide();}
if(m.autoHideTimer){clearTimeout(m.autoHideTimer);delete m.autoHideTimer;}}
function onBeforeShow(m){var pm=m.parentMenu;if(!pm&&!m.allowOtherMenus){hideAll();}else if(pm&&pm.activeChild){pm.activeChild.hide();}}
function onMouseDown(e){if(active.length>0&&!e.getTarget(".x-menu")){hideAll();}}
function onBeforeCheck(mi,state){if(state){var g=groups[mi.group];for(var i=0,l=g.length;i<l;i++){if(g[i]!=mi){g[i].setChecked(false);}}}}
return{hideAll:function(){hideAll();},register:function(menu){if(!menus){init();}
menus[menu.id]=menu;menu.on("beforehide",onBeforeHide);menu.on("hide",onHide);menu.on("beforeshow",onBeforeShow);menu.on("show",onShow);var g=menu.group;if(g&&menu.events["checkchange"]){if(!groups[g]){groups[g]=[];}
groups[g].push(menu);menu.on("checkchange",onCheck);}},get:function(menu){if(typeof menu=="string"){return menus[menu];}else if(menu.events){return menu;}else{return new Ext.menu.Menu(menu);}},unregister:function(menu){delete menus[menu.id];menu.un("beforehide",onBeforeHide);menu.un("hide",onHide);menu.un("beforeshow",onBeforeShow);menu.un("show",onShow);var g=menu.group;if(g&&menu.events["checkchange"]){groups[g].remove(menu);menu.un("checkchange",onCheck);}},registerCheckable:function(menuItem){var g=menuItem.group;if(g){if(!groups[g]){groups[g]=[];}
groups[g].push(menuItem);menuItem.on("beforecheckchange",onBeforeCheck);}},unregisterCheckable:function(menuItem){var g=menuItem.group;if(g){groups[g].remove(menuItem);menuItem.un("beforecheckchange",onBeforeCheck);}}};}();

Ext.menu.BaseItem=function(config){Ext.menu.BaseItem.superclass.constructor.call(this,config);this.addEvents({click:true,activate:true,deactivate:true});if(this.handler){this.on("click",this.handler,this.scope,true);}};Ext.extend(Ext.menu.BaseItem,Ext.Component,{canActivate:false,activeClass:"x-menu-item-active",hideOnClick:true,hideDelay:100,ctype:"Ext.menu.BaseItem",actionMode:"container",render:function(container,parentMenu){this.parentMenu=parentMenu;Ext.menu.BaseItem.superclass.render.call(this,container);this.container.menuItemId=this.id;},onRender:function(container){this.el=Ext.get(this.el);container.dom.appendChild(this.el.dom);},onClick:function(e){if(!this.disabled&&this.fireEvent("click",this,e)!==false&&this.parentMenu.fireEvent("itemclick",this,e)!==false){this.handleClick(e);}else{e.stopEvent();}},activate:function(){if(this.disabled){return false;}
var li=this.container;li.addClass(this.activeClass);this.region=li.getRegion().adjust(2,2,-2,-2);this.fireEvent("activate",this);return true;},deactivate:function(){this.container.removeClass(this.activeClass);this.fireEvent("deactivate",this);},shouldDeactivate:function(e){return!this.region||!this.region.contains(e.getPoint());},handleClick:function(e){if(this.hideOnClick){this.parentMenu.hide.defer(this.hideDelay,this.parentMenu,[true]);}},expandMenu:function(autoActivate){},hideMenu:function(){}});

Ext.menu.TextItem=function(text){this.text=text;Ext.menu.TextItem.superclass.constructor.call(this);};Ext.extend(Ext.menu.TextItem,Ext.menu.BaseItem,{hideOnClick:false,itemCls:"x-menu-text",onRender:function(){var s=document.createElement("span");s.className=this.itemCls;s.innerHTML=this.text;this.el=s;Ext.menu.TextItem.superclass.onRender.apply(this,arguments);}});

Ext.menu.Separator=function(config){Ext.menu.Separator.superclass.constructor.call(this,config);};Ext.extend(Ext.menu.Separator,Ext.menu.BaseItem,{itemCls:"x-menu-sep",hideOnClick:false,onRender:function(li){var s=document.createElement("span");s.className=this.itemCls;s.innerHTML="&#160;";this.el=s;li.addClass("x-menu-sep-li");Ext.menu.Separator.superclass.onRender.apply(this,arguments);}});

Ext.menu.Item=function(config){Ext.menu.Item.superclass.constructor.call(this,config);if(this.menu){this.menu=Ext.menu.MenuMgr.get(this.menu);}};Ext.extend(Ext.menu.Item,Ext.menu.BaseItem,{itemCls:"x-menu-item",canActivate:true,ctype:"Ext.menu.Item",onRender:function(container){var el=document.createElement("a");el.hideFocus=true;el.unselectable="on";el.href=this.href||"#";if(this.hrefTarget){el.target=this.hrefTarget;}
el.className=this.itemCls+(this.menu?" x-menu-item-arrow":"")+(this.cls?" "+this.cls:"");el.innerHTML=String.format('<img src="{0}" class="x-menu-item-icon">{1}',this.icon||Ext.BLANK_IMAGE_URL,this.text);this.el=el;Ext.menu.Item.superclass.onRender.call(this,container);},setText:function(text){this.text=text;if(this.rendered){this.el.update(String.format('<img src="{0}" class="x-menu-item-icon">{1}',this.icon||Ext.BLANK_IMAGE_URL,this.text));this.parentMenu.autoWidth();}},handleClick:function(e){if(!this.href){e.stopEvent();}
Ext.menu.Item.superclass.handleClick.apply(this,arguments);},activate:function(autoExpand){if(Ext.menu.Item.superclass.activate.apply(this,arguments)){this.focus();if(autoExpand){this.expandMenu();}}
return true;},shouldDeactivate:function(e){if(Ext.menu.Item.superclass.shouldDeactivate.call(this,e)){if(this.menu&&this.menu.isVisible()){return!this.menu.getEl().getRegion().contains(e.getPoint());}
return true;}
return false;},deactivate:function(){Ext.menu.Item.superclass.deactivate.apply(this,arguments);this.hideMenu();},expandMenu:function(autoActivate){if(!this.disabled&&this.menu){if(!this.menu.isVisible()){this.menu.show(this.container,this.parentMenu.subMenuAlign||"tl-tr?",this.parentMenu);}
if(autoActivate){this.menu.tryActivate(0,1);}}},hideMenu:function(){if(this.menu&&this.menu.isVisible()){this.menu.hide();}}});

Ext.menu.CheckItem=function(config){Ext.menu.CheckItem.superclass.constructor.call(this,config);this.addEvents({"beforecheckchange":true,"checkchange":true});if(this.checkHandler){this.on('checkchange',this.checkHandler,this.scope);}};Ext.extend(Ext.menu.CheckItem,Ext.menu.Item,{itemCls:"x-menu-item x-menu-check-item",groupClass:"x-menu-group-item",checked:false,ctype:"Ext.menu.CheckItem",onRender:function(c){Ext.menu.CheckItem.superclass.onRender.apply(this,arguments);if(this.group){this.el.addClass(this.groupClass);}
Ext.menu.MenuMgr.registerCheckable(this);if(this.checked){this.checked=false;this.setChecked(true,true);}},destroy:function(){if(this.rendered){Ext.menu.MenuMgr.unregisterCheckable(this);}
Ext.menu.CheckItem.superclass.destroy.apply(this,arguments);},setChecked:function(state,suppressEvent){if(this.checked!=state&&this.fireEvent("beforecheckchange",this,state)!==false){if(this.container){this.container[state?"addClass":"removeClass"]("x-menu-item-checked");}
this.checked=state;if(suppressEvent!==true){this.fireEvent("checkchange",this,state);}}},handleClick:function(e){if(!this.disabled&&!(this.checked&&this.group)){this.setChecked(!this.checked);}
Ext.menu.CheckItem.superclass.handleClick.apply(this,arguments);}});

Ext.menu.Adapter=function(component,config){Ext.menu.Adapter.superclass.constructor.call(this,config);this.component=component;};Ext.extend(Ext.menu.Adapter,Ext.menu.BaseItem,{canActivate:true,onRender:function(container){this.component.render(container);this.el=this.component.getEl();},activate:function(){if(this.disabled){return false;}
this.component.focus();this.fireEvent("activate",this);return true;},deactivate:function(){this.fireEvent("deactivate",this);},disable:function(){this.component.disable();Ext.menu.Adapter.superclass.disable.call(this);},enable:function(){this.component.enable();Ext.menu.Adapter.superclass.enable.call(this);}});

Ext.menu.DateItem=function(config){Ext.menu.DateItem.superclass.constructor.call(this,new Ext.DatePicker(config),config);this.picker=this.component;this.addEvents({select:true});this.picker.on("render",function(picker){picker.getEl().swallowEvent("click");picker.container.addClass("x-menu-date-item");});this.picker.on("select",this.onSelect,this);};Ext.extend(Ext.menu.DateItem,Ext.menu.Adapter,{onSelect:function(picker,date){this.fireEvent("select",this,date,picker);Ext.menu.DateItem.superclass.handleClick.call(this);}});

Ext.menu.ColorItem=function(config){Ext.menu.ColorItem.superclass.constructor.call(this,new Ext.ColorPalette(config),config);this.palette=this.component;this.relayEvents(this.palette,["select"]);if(this.selectHandler){this.on('select',this.selectHandler,this.scope);}};Ext.extend(Ext.menu.ColorItem,Ext.menu.Adapter);

Ext.menu.DateMenu=function(config){Ext.menu.DateMenu.superclass.constructor.call(this,config);this.plain=true;var di=new Ext.menu.DateItem(config);this.add(di);this.picker=di.picker;this.relayEvents(di,["select"]);};Ext.extend(Ext.menu.DateMenu,Ext.menu.Menu);

Ext.menu.ColorMenu=function(config){Ext.menu.ColorMenu.superclass.constructor.call(this,config);this.plain=true;var ci=new Ext.menu.ColorItem(config);this.add(ci);this.palette=ci.palette;this.relayEvents(ci,["select"]);};Ext.extend(Ext.menu.ColorMenu,Ext.menu.Menu);
