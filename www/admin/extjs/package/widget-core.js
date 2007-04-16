/*
 * Ext JS Library 1.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext.ComponentMgr=function(){var all=new Ext.util.MixedCollection();return{register:function(c){all.add(c);},unregister:function(c){all.remove(c);},get:function(id){return all.get(id);},onAvailable:function(id,fn,scope){all.on("add",function(index,o){if(o.id==id){fn.call(scope||o,o);all.un("add",fn,scope);}});}};}();Ext.Component=function(config){config=config||{};if(config.tagName||config.dom||typeof config=="string"){config={el:config,id:config.id||config};}
Ext.apply(this,config);this.addEvents({disable:true,enable:true,beforeshow:true,show:true,beforehide:true,hide:true,beforerender:true,render:true,beforedestroy:true,destroy:true});if(!this.id){this.id="ext-comp-"+(++Ext.Component.AUTO_ID);}
Ext.ComponentMgr.register(this);Ext.Component.superclass.constructor.call(this);};Ext.Component.AUTO_ID=1000;Ext.extend(Ext.Component,Ext.util.Observable,{hidden:false,disabled:false,disabledClass:"x-item-disabled",rendered:false,ctype:"Ext.Component",actionMode:"el",getActionEl:function(){return this[this.actionMode];},render:function(container){if(!this.rendered&&this.fireEvent("beforerender",this)!==false){this.container=Ext.get(container);this.rendered=true;this.onRender(this.container);if(this.cls){this.el.addClass(this.cls);delete this.cls;}
this.fireEvent("render",this);if(this.hidden){this.hide();}
if(this.disabled){this.disable();}
this.afterRender(this.container);}},onRender:function(ct){this.el=Ext.get(this.el);ct.dom.appendChild(this.el.dom);},getAutoCreate:function(){var cfg=typeof this.autoCreate=="object"?this.autoCreate:Ext.apply({},this.defaultAutoCreate);if(this.id&&!cfg.id){cfg.id=this.id;}
return cfg;},afterRender:Ext.emptyFn,destroy:function(){if(this.fireEvent("beforedestroy",this)!==false){this.purgeListeners();if(this.rendered){this.el.removeAllListeners();this.el.remove();if(this.actionMode=="container"){this.container.remove();}}
this.onDestroy();Ext.ComponentMgr.unregister(this);this.fireEvent("destroy",this);}},onDestroy:function(){},getEl:function(){return this.el;},focus:function(selectText){if(this.rendered){this.el.focus();if(selectText===true){this.el.dom.select();}}},blur:function(){if(this.rendered){this.el.blur();}},disable:function(){if(this.rendered){this.getActionEl().addClass(this.disabledClass);this.el.dom.disabled=true;}
this.disabled=true;this.fireEvent("disable",this);},enable:function(){if(this.rendered){this.getActionEl().removeClass(this.disabledClass);this.el.dom.disabled=false;}
this.disabled=false;this.fireEvent("enable",this);},setDisabled:function(disabled){this[disabled?"disable":"enable"]();},show:function(){if(this.fireEvent("beforeshow",this)!==false){this.hidden=false;if(this.rendered){this.onShow();}
this.fireEvent("show",this);}},onShow:function(){var st=this.getActionEl().dom.style;st.display="";st.visibility="visible";},hide:function(){if(this.fireEvent("beforehide",this)!==false){this.hidden=true;if(this.rendered){this.onHide();}
this.fireEvent("hide",this);}},onHide:function(){this.getActionEl().dom.style.display="none";},setVisible:function(visible){if(visible){this.show();}else{this.hide();}}});

(function(){Ext.Layer=function(config,existingEl){config=config||{};var dh=Ext.DomHelper;var cp=config.parentEl,pel=cp?Ext.getDom(cp):document.body;if(existingEl){this.dom=Ext.getDom(existingEl);}
if(!this.dom){var o=config.dh||{tag:"div",cls:"x-layer"};this.dom=dh.append(pel,o);}
if(config.cls){this.addClass(config.cls);}
this.constrain=config.constrain!==false;this.visibilityMode=Ext.Element.VISIBILITY;if(config.id){this.id=this.dom.id=config.id;}else{this.id=Ext.id(this.dom);}
var zindex=(config.zindex||parseInt(this.getStyle("z-index"),10))||11000;this.position("absolute",zindex);if(config.shadow){this.shadowOffset=config.shadowOffset||4;this.shadow=new Ext.Shadow({offset:this.shadowOffset,mode:config.shadow});}else{this.shadowOffset=0;}
if(config.shim!==false&&Ext.useShims){this.shim=this.createShim();this.shim.setOpacity(0);this.shim.position("absolute",zindex-2);}
this.useDisplay=config.useDisplay;this.hide();};var supr=Ext.Element.prototype;Ext.extend(Ext.Layer,Ext.Element,{sync:function(doShow){var sw=this.shadow,sh=this.shim;if(!this.updating&&this.isVisible()&&(sw||sh)){var w=this.getWidth(),h=this.getHeight();var l=this.getLeft(true),t=this.getTop(true);if(sw){if(doShow&&!sw.isVisible()){sw.show(this);}else{sw.realign(l,t,w,h);}
if(sh){if(doShow){sh.show();}
var a=sw.adjusts,s=sh.dom.style;s.left=(l+a.l)+"px";s.top=(t+a.t)+"px";s.width=(w+a.w)+"px";s.height=(h+a.h)+"px";}}else if(sh){if(doShow){sh.show();}
sh.setSize(w,h);sh.setLeftTop(l,t);}}},destroy:function(){if(this.shim){this.shim.remove();}
if(this.shadow){this.shadow.hide();}
this.removeAllListeners();this.remove();},beginUpdate:function(){this.updating=true;},endUpdate:function(){this.updating=false;this.sync(true);},hideUnders:function(negOffset){if(this.shadow){this.shadow.hide();}
if(this.shim){this.shim.hide();if(negOffset){this.shim.setLeftTop(-10000,-10000);}}},constrainXY:function(){if(this.constrain){var vw=Ext.lib.Dom.getViewWidth(),vh=Ext.lib.Dom.getViewHeight();var s=Ext.get(document).getScroll();xy=this.getXY();var x=xy[0],y=xy[1];var w=this.dom.offsetWidth+this.shadowOffset,h=this.dom.offsetHeight+this.shadowOffset;var moved=false;if((x+w)>vw+s.left){x=vw-w-this.shadowOffset;moved=true;}
if((y+h)>vh+s.top){y=vh-h-this.shadowOffset;moved=true;}
if(x<s.left){x=s.left;moved=true;}
if(y<s.top){y=s.top;moved=true;}
if(moved){if(this.avoidY){var ay=this.avoidY;if(y<=ay&&(y+h)>=ay){y=ay-h-5;}}
xy=[x,y];this.lastXY=xy;supr.setXY.call(this,xy);this.sync();}}},showAction:function(){if(this.useDisplay===true){this.setDisplayed("");}else if(this.lastXY){supr.setXY.call(this,this.lastXY);}},hideAction:function(){if(this.useDisplay===true){this.setDisplayed(false);}else{this.setLeftTop(-10000,-10000);}},setVisible:function(v,a,d,c,e){this.showAction();if(a&&v){var cb=function(){this.sync(true);if(c){c();}}.createDelegate(this);supr.setVisible.call(this,true,true,d,cb,e);}else{if(!v){this.hideUnders(true);}
var cb=c;if(a){cb=function(){this.hideAction();if(c){c();}}.createDelegate(this);}
supr.setVisible.call(this,v,a,d,cb,e);if(v){this.sync(true);}else if(!a){this.hideAction();}}},beforeFx:function(){this.beforeAction();return Ext.Layer.superclass.beforeFx.apply(this,arguments);},afterFx:function(){Ext.Layer.superclass.afterFx.apply(this,arguments);this.sync(this.isVisible());},beforeAction:function(){if(!this.updating&&this.shadow){this.shadow.hide();}},setXY:function(xy,a,d,c,e){this.fixDisplay();this.beforeAction();this.lastXY=xy;var cb=this.createCB(c);supr.setXY.call(this,xy,a,d,cb,e);if(!a){cb();}},createCB:function(c){var el=this;return function(){el.constrainXY();el.sync(true);if(c){c();}};},setX:function(x,a,d,c,e){this.setXY([x,this.getY()],a,d,c,e);},setY:function(y,a,d,c,e){this.setXY([this.getX(),y],a,d,c,e);},setSize:function(w,h,a,d,c,e){this.beforeAction();var cb=this.createCB(c);supr.setSize.call(this,w,h,a,d,cb,e);if(!a){cb();}},setWidth:function(w,a,d,c,e){this.beforeAction();var cb=this.createCB(c);supr.setWidth.call(this,w,a,d,cb,e);if(!a){cb();}},setHeight:function(h,a,d,c,e){this.beforeAction();var cb=this.createCB(c);supr.setHeight.call(this,h,a,d,cb,e);if(!a){cb();}},setBounds:function(x,y,w,h,a,d,c,e){this.beforeAction();var cb=this.createCB(c);if(!a){supr.setXY.call(this,[x,y]);supr.setSize.call(this,w,h,a,d,cb,e);cb();}else{supr.setBounds.call(this,x,y,w,h,a,d,cb,e);}
return this;},setZIndex:function(zindex){this.setStyle("z-index",zindex+2);if(this.shadow){this.shadow.setZIndex(zindex+1);}
if(this.shim){this.shim.setStyle("z-index",zindex);}}});})();

Ext.Shadow=function(config){Ext.apply(this,config);if(typeof this.mode!="string"){this.mode=this.defaultMode;}
var o=this.offset,a={h:0};switch(this.mode.toLowerCase()){case"drop":a.w=0;a.l=a.t=o;break;case"sides":a.w=(o*2);a.l=-o;a.t=o;break;case"frame":a.w=a.h=(o*2);a.l=a.t=-o;break;};this.adjusts=a;};Ext.Shadow.prototype={offset:4,defaultMode:"drop",show:function(target){target=Ext.get(target);if(!this.el){this.el=Ext.Shadow.Pool.pull();if(this.el.dom.nextSibling!=target.dom){this.el.insertBefore(target);}}
this.el.setStyle("z-index",this.zIndex||parseInt(target.getStyle("z-index"),10)-1);this.realign(target.getLeft(true),target.getTop(true),target.getWidth(),target.getHeight());this.el.dom.style.display="block";},isVisible:function(){return this.el?true:false;},realign:function(l,t,w,h){var a=this.adjusts,d=this.el.dom,s=d.style;s.left=(l+a.l)+"px";s.top=(t+a.t)+"px";var sw=(w+a.w),sh=(h+a.h),sws=sw+"px",shs=sh+"px";if(s.width!=sws||s.height!=shs){s.width=sws;s.height=shs;var cn=d.childNodes;var sww=Math.max(0,(sw-12))+"px";cn[0].childNodes[1].style.width=sww;cn[1].childNodes[1].style.width=sww;cn[2].childNodes[1].style.width=sww;cn[1].style.height=Math.max(0,(sh-12))+"px";}},hide:function(){if(this.el){this.el.dom.style.display="none";Ext.Shadow.Pool.push(this.el);delete this.el;}},setZIndex:function(z){this.zIndex=z;if(this.el){this.el.setStyle("z-index",z);}}};Ext.Shadow.Pool=function(){var p=[];var markup='<div class="x-shadow"><div class="xst"><div class="xstl"></div><div class="xstc"></div><div class="xstr"></div></div><div class="xsc"><div class="xsml"></div><div class="xsmc"></div><div class="xsmr"></div></div><div class="xsb"><div class="xsbl"></div><div class="xsbc"></div><div class="xsbr"></div></div></div>';return{pull:function(){var sh=p.shift();if(!sh){sh=Ext.get(Ext.DomHelper.insertHtml("beforeBegin",document.body.firstChild,markup));if(Ext.isIE&&!Ext.isIE7){sh.setOpacity(.3);}}
return sh;},push:function(sh){p.push(sh);}};}();
