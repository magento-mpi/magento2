/*
 * Ext JS Library 1.0 Beta 2
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


Ext={};window["undefined"]=window["undefined"];Ext.apply=function(o,c,defaults){if(defaults){Ext.apply(o,defaults);}
if(o&&c&&typeof c=='object'){for(var p in c){o[p]=c[p];}}
return o;};(function(){var idSeed=0;var ua=navigator.userAgent.toLowerCase();var isStrict=document.compatMode=="CSS1Compat",isOpera=ua.indexOf("opera")>-1,isSafari=ua.indexOf("webkit")>-1,isIE=ua.indexOf("msie")>-1,isIE7=ua.indexOf("msie 7")>-1,isGecko=!isSafari&&ua.indexOf("gecko")>-1,isBorderBox=isIE&&!isStrict,isWindows=(ua.indexOf("windows")!=-1||ua.indexOf("win32")!=-1),isMac=(ua.indexOf("macintosh")!=-1||ua.indexOf("mac os x")!=-1);if(isIE&&!isIE7){try{document.execCommand("BackgroundImageCache",false,true);}catch(e){}}
Ext.apply(Ext,{isStrict:isStrict,SSL_SECURE_URL:"javascript:false",BLANK_IMAGE_URL:"http:/"+"/extjs.com/s.gif",emptyFn:function(){},applyIf:function(o,c){if(o&&c){for(var p in c){if(typeof o[p]=="undefined"){o[p]=c[p];}}}
return o;},id:function(el,prefix){prefix=prefix||"ext-gen";el=Ext.getDom(el);var id=prefix+(++idSeed);return el?(el.id?el.id:(el.id=id)):id;},extend:function(){var io=function(o){for(var m in o){this[m]=o[m];}};return function(sc,sp,overrides){var F=function(){},scp,spp=sp.prototype;F.prototype=spp;scp=sc.prototype=new F();scp.constructor=sc;sc.superclass=spp;if(spp.constructor==Object.prototype.constructor){spp.constructor=sp;}
sc.override=function(o){Ext.override(sc,o);};scp.override=io;Ext.override(sc,overrides);return sc;};}(),override:function(origclass,overrides){if(overrides){var p=origclass.prototype;for(var method in overrides){p[method]=overrides[method];}}},namespace:function(){var a=arguments,o=null,i,j,d,rt;for(i=0;i<a.length;++i){d=a[i].split(".");rt=d[0];eval('if (typeof '+rt+' == "undefined"){'+rt+' = {};} o = '+rt+';');for(j=1;j<d.length;++j){o[d[j]]=o[d[j]]||{};o=o[d[j]];}}},urlEncode:function(o){if(!o){return"";}
var buf=[];for(var key in o){var ov=o[key];var type=typeof ov;if(type=='undefined'){buf.push(encodeURIComponent(key),"=&");}else if(type!="function"&&type!="object"){buf.push(encodeURIComponent(key),"=",encodeURIComponent(ov),"&");}else if(ov instanceof Array){for(var i=0,len=ov.length;i<len;i++){buf.push(encodeURIComponent(key),"=",encodeURIComponent(ov[i]===undefined?'':ov[i]),"&");}}}
buf.pop();return buf.join("");},urlDecode:function(string,overwrite){if(!string||!string.length){return{};}
var obj={};var pairs=string.split('&');var pair,name,value;for(var i=0,len=pairs.length;i<len;i++){pair=pairs[i].split('=');name=pair[0];value=pair[1];if(overwrite!==true){if(typeof obj[name]=="undefined"){obj[name]=value;}else if(typeof obj[name]=="string"){obj[name]=[obj[name]];obj[name].push(value);}else{obj[name].push(value);}}else{obj[name]=value;}}
return obj;},each:function(array,fn,scope){if(typeof array.length=="undefined"||typeof array=="string"){array=[array];}
for(var i=0,len=array.length;i<len;i++){if(fn.call(scope||array[i],array[i],i,array)===false){return i;};}},combine:function(){var as=arguments,l=as.length,r=[];for(var i=0;i<l;i++){var a=as[i];if(a instanceof Array){r=r.concat(a);}else if(a.length!==undefined&&!a.substr){r=r.concat(Array.prototype.slice.call(a,0));}else{r.push(a);}}
return r;},escapeRe:function(s){return s.replace(/([.*+?^${}()|[\]\/\\])/g,"\\$1");},callback:function(cb,scope,args,delay){if(typeof cb=="function"){if(delay){cb.defer(delay,scope,args||[]);}else{cb.apply(scope,args||[]);}}},getDom:function(el){if(!el){return null;}
return el.dom?el.dom:(typeof el=='string'?document.getElementById(el):el);},num:function(v,defaultValue){if(typeof v!='number'){return defaultValue;}
return v;},isOpera:isOpera,isSafari:isSafari,isIE:isIE,isIE7:isIE7,isGecko:isGecko,isBorderBox:isBorderBox,isWindows:isWindows,isMac:isMac,useShims:((isIE&&!isIE7)||(isGecko&&isMac))});})();Ext.namespace("Ext","Ext.util","Ext.grid","Ext.dd","Ext.tree","Ext.data","Ext.form","Ext.menu","Ext.state","Ext.lib");Ext.apply(Function.prototype,{createCallback:function(){var args=arguments;var method=this;return function(){return method.apply(window,args);};},createDelegate:function(obj,args,appendArgs){var method=this;return function(){var callArgs=args||arguments;if(appendArgs===true){callArgs=Array.prototype.slice.call(arguments,0);callArgs=callArgs.concat(args);}else if(typeof appendArgs=="number"){callArgs=Array.prototype.slice.call(arguments,0);var applyArgs=[appendArgs,0].concat(args);Array.prototype.splice.apply(callArgs,applyArgs);}
return method.apply(obj||window,callArgs);};},defer:function(millis,obj,args,appendArgs){var fn=this.createDelegate(obj,args,appendArgs);if(millis){return setTimeout(fn,millis);}
fn();return 0;},createSequence:function(fcn,scope){if(typeof fcn!="function"){return this;}
var method=this;return function(){var retval=method.apply(this||window,arguments);fcn.apply(scope||this||window,arguments);return retval;};},createInterceptor:function(fcn,scope){if(typeof fcn!="function"){return this;}
var method=this;return function(){fcn.target=this;fcn.method=method;if(fcn.apply(scope||this||window,arguments)===false){return;}
return method.apply(this||window,arguments);};}});Ext.applyIf(String,{escape:function(string){return string.replace(/('|\\)/g,"\\$1");},leftPad:function(val,size,ch){var result=new String(val);if(ch==null){ch=" ";}
while(result.length<size){result=ch+result;}
return result;},format:function(format){var args=Array.prototype.slice.call(arguments,1);return format.replace(/\{(\d+)\}/g,function(m,i){return args[i];});}});String.prototype.toggle=function(value,other){return this==value?other:value;};Ext.applyIf(Number.prototype,{constrain:function(min,max){return Math.min(Math.max(this,min),max);}});Ext.applyIf(Array.prototype,{indexOf:function(o){for(var i=0,len=this.length;i<len;i++){if(this[i]==o)return i;}
return-1;},remove:function(o){var index=this.indexOf(o);if(index!=-1){this.splice(index,1);}}});Date.prototype.getElapsed=function(date){return Math.abs((date||new Date()).getTime()-this.getTime());};

(function(){var libFlyweight;Ext.lib.Dom={getViewWidth:function(full){return full?this.getDocumentWidth():this.getViewportWidth();},getViewHeight:function(full){return full?this.getDocumentHeight():this.getViewportHeight();},getDocumentHeight:function(){var scrollHeight=(document.compatMode!="CSS1Compat")?document.body.scrollHeight:document.documentElement.scrollHeight;return Math.max(scrollHeight,this.getViewportHeight());},getDocumentWidth:function(){var scrollWidth=(document.compatMode!="CSS1Compat")?document.body.scrollWidth:document.documentElement.scrollWidth;return Math.max(scrollWidth,this.getViewportWidth());},getViewportHeight:function(){var height=self.innerHeight;var mode=document.compatMode;if((mode||Ext.isIE)&&!Ext.isOpera){height=(mode=="CSS1Compat")?document.documentElement.clientHeight:document.body.clientHeight;}
return height;},getViewportWidth:function(){var width=self.innerWidth;var mode=document.compatMode;if(mode||Ext.isIE){width=(mode=="CSS1Compat")?document.documentElement.clientWidth:document.body.clientWidth;}
return width;},isAncestor:function(p,c){p=Ext.getDom(p);c=Ext.getDom(c);if(!p||!c){return false;}
if(p.contains&&!Ext.isSafari){return p.contains(c);}else if(p.compareDocumentPosition){return!!(p.compareDocumentPosition(c)&16);}else{var parent=c.parentNode;while(parent){if(parent==p){return true;}
else if(!parent.tagName||parent.tagName.toUpperCase()=="HTML"){return false;}
parent=parent.parentNode;}
return false;}},getRegion:function(el){return Ext.lib.Region.getRegion(el);},getY:function(el){return this.getXY(el)[1];},getX:function(el){return this.getXY(el)[0];},getXY:function(el){var p,pe,b,scroll,bd=document.body;el=Ext.getDom(el);if(el.getBoundingClientRect){b=el.getBoundingClientRect();scroll=fly(document).getScroll();return[b.left+scroll.left,b.top+scroll.top];}else{var x=el.offsetLeft,y=el.offsetTop;p=el.offsetParent;var hasAbsolute=false;if(p!=el){while(p){x+=p.offsetLeft;y+=p.offsetTop;if(Ext.isSafari&&!hasAbsolute&&fly(p).getStyle("position")=="absolute"){hasAbsolute=true;}
if(Ext.isGecko){pe=fly(p);var bt=parseInt(pe.getStyle("borderTopWidth"),10)||0;var bl=parseInt(pe.getStyle("borderLeftWidth"),10)||0;x+=bl;y+=bt;if(p!=el&&pe.getStyle('overflow')!='visible'){x+=bl;y+=bt;}}
p=p.offsetParent;}}
if(Ext.isSafari&&(hasAbsolute||fly(el).getStyle("position")=="absolute")){x-=bd.offsetLeft;y-=bd.offsetTop;}}
p=el.parentNode;while(p&&p!=bd){if(!Ext.isOpera||(Ext.isOpera&&p.tagName!='TR'&&fly(p).getStyle("display")!="inline")){x-=p.scrollLeft;y-=p.scrollTop;}
p=p.parentNode;}
return[x,y];},setXY:function(el,xy){el=Ext.fly(el,'_setXY');el.position();var pts=el.translatePoints(xy);if(xy[0]!==false){el.dom.style.left=pts.left+"px";}
if(xy[1]!==false){el.dom.style.top=pts.top+"px";}},setX:function(el,x){this.setXY(el,[x,false]);},setY:function(el,y){this.setXY(el,[false,y]);}};Ext.lib.Event={getPageX:function(e){return Event.pointerX(e.browserEvent||e);},getPageY:function(e){return Event.pointerY(e.browserEvent||e);},getXY:function(e){e=e.browserEvent||e;return[Event.pointerX(e),Event.pointerY(e)];},getTarget:function(e){return Event.element(e.browserEvent||e);},resolveTextNode:function(node){if(node&&3==node.nodeType){return node.parentNode;}else{return node;}},getRelatedTarget:function(ev){ev=ev.browserEvent||ev;var t=ev.relatedTarget;if(!t){if(ev.type=="mouseout"){t=ev.toElement;}else if(ev.type=="mouseover"){t=ev.fromElement;}}
return this.resolveTextNode(t);},on:function(el,eventName,fn){Event.observe(el,eventName,fn,false);},un:function(el,eventName,fn){Event.stopObserving(el,eventName,fn,false);},purgeElement:function(el){},preventDefault:function(e){e=e.browserEvent||e;if(e.preventDefault){e.preventDefault();}else{e.returnValue=false;}},stopPropagation:function(e){e=e.browserEvent||e;if(e.stopPropagation){e.stopPropagation();}else{e.cancelBubble=true;}},stopEvent:function(e){Event.stop(e.browserEvent||e);},onAvailable:function(el,fn,scope,override){var start=new Date(),iid;var f=function(){if(start.getElapsed()>10000){clearInterval(iid);}
var el=document.getElementById(id);if(el){clearInterval(iid);fn.call(scope||window,el);}};iid=setInterval(f,50);}};Ext.lib.Ajax=function(){var createSuccess=function(cb){return cb.success?function(xhr){cb.success.call(cb.scope||window,{responseText:xhr.responseText,responseXML:xhr.responseXML,argument:cb.argument});}:Ext.emptyFn;};var createFailure=function(cb){return cb.failure?function(xhr){cb.failure.call(cb.scope||window,{responseText:xhr.responseText,responseXML:xhr.responseXML,argument:cb.argument});}:Ext.emptyFn;};return{request:function(method,uri,cb,data){new Ajax.Request(uri,{method:method,parameters:data||'',timeout:cb.timeout,onSuccess:createSuccess(cb),onFailure:createFailure(cb)});},formRequest:function(form,uri,cb,data,isUpload,sslUri){new Ajax.Request(uri,{method:'POST',parameters:Form.serialize(form)+(data?'&'+data:''),timeout:cb.timeout,onSuccess:createSuccess(cb),onFailure:createFailure(cb)});},isCallInProgress:function(trans){return false;},abort:function(trans){return false;},serializeForm:function(form){return Form.serialize(form.dom||form,true);}};}();Ext.lib.Anim=function(){var easings={easeOut:function(pos){return 1-Math.pow(1-pos,2);},easeIn:function(pos){return 1-Math.pow(1-pos,2);}};var createAnim=function(cb,scope){return{stop:function(skipToLast){this.effect.cancel();},isAnimated:function(){return this.effect.state=='running';},proxyCallback:function(){Ext.callback(cb,scope);}};};return{scroll:function(el,args,duration,easing,cb,scope){var anim=createAnim(cb,scope);el=Ext.getDom(el);el.scrollLeft=args.to[0];el.scrollTop=args.to[1];anim.proxyCallback();return anim;},motion:function(el,args,duration,easing,cb,scope){return this.run(el,args,duration,easing,cb,scope);},color:function(el,args,duration,easing,cb,scope){return this.run(el,args,duration,easing,cb,scope);},run:function(el,args,duration,easing,cb,scope,type){var o={};for(var k in args){switch(k){case'points':var by,pts,e=Ext.fly(el,'_animrun');e.position();if(by=args.points.by){var xy=e.getXY();pts=e.translatePoints([xy[0]+by[0],xy[1]+by[1]]);}else{pts=e.translatePoints(args.points.to);}
o.left=pts.left+'px';o.top=pts.top+'px';break;case'width':o.width=args.width.to+'px';break;case'height':o.height=args.height.to+'px';break;case'opacity':o.opacity=String(args.opacity.to);break;default:o[k]=String(args[k].to);break;}}
var anim=createAnim(cb,scope);anim.effect=new Effect.Morph(Ext.id(el),{duration:duration,afterFinish:anim.proxyCallback,transition:easings[easing]||Effect.Transitions.linear,style:o});return anim;}};}();function fly(el){if(!libFlyweight){libFlyweight=new Ext.Element.Flyweight();}
libFlyweight.dom=el;return libFlyweight;}
Ext.lib.Region=function(t,r,b,l){this.top=t;this[1]=t;this.right=r;this.bottom=b;this.left=l;this[0]=l;};Ext.lib.Region.prototype={contains:function(region){return(region.left>=this.left&&region.right<=this.right&&region.top>=this.top&&region.bottom<=this.bottom);},getArea:function(){return((this.bottom-this.top)*(this.right-this.left));},intersect:function(region){var t=Math.max(this.top,region.top);var r=Math.min(this.right,region.right);var b=Math.min(this.bottom,region.bottom);var l=Math.max(this.left,region.left);if(b>=t&&r>=l){return new Ext.lib.Region(t,r,b,l);}else{return null;}},union:function(region){var t=Math.min(this.top,region.top);var r=Math.max(this.right,region.right);var b=Math.max(this.bottom,region.bottom);var l=Math.min(this.left,region.left);return new Ext.lib.Region(t,r,b,l);},adjust:function(t,l,b,r){this.top+=t;this.left+=l;this.right+=r;this.bottom+=b;return this;}};Ext.lib.Region.getRegion=function(el){var p=Ext.lib.Dom.getXY(el);var t=p[1];var r=p[0]+el.offsetWidth;var b=p[1]+el.offsetHeight;var l=p[0];return new Ext.lib.Region(t,r,b,l);};Ext.lib.Point=function(x,y){if(x instanceof Array){y=x[1];x=x[0];}
this.x=this.right=this.left=this[0]=x;this.y=this.top=this.bottom=this[1]=y;};Ext.lib.Point.prototype=new Ext.lib.Region();if(Ext.isIE){Event.observe(window,"unload",function(){var p=Function.prototype;delete p.createSequence;delete p.defer;delete p.createDelegate;delete p.createCallback;delete p.createInterceptor;});}})();
