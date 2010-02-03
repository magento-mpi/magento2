/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

Flex = {};
Flex.currentID = 0;
Flex.uniqId = function() {
    return 'flexMovieUID'+( ++Flex.currentID );
}

Flex.Object = Class.create();

Object.extend( Flex.Object.prototype, {
			initialize: function ( config ) {
                this.isIE  = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
            	this.isWin = (navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
            	this.isOpera = (navigator.userAgent.indexOf("Opera") != -1) ? true : false;
            	this.attributes = {
            		 quality:"high",
            		 pluginspage: "http://www.adobe.com/go/getflashplayer",
            		 type: "application/x-shockwave-flash",
            		 allowScriptAccess: "always",
                     classid: "clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
            	};
            	this.bridgeName = '';
            	this.bridge = false;
            	this.setAttributes( config );
            	this.applied = false;

            	var myTemplatesPattern = /(^|.|\r|\n)(\{(.*?)\})/;
            	if(this.detectFlashVersion(9, 0, 28)) {
            		if(this.isIE && !this.isOpera) {
            			this.template = new Template( '<object {objectAttributes}><param name="allowFullScreen" value="true"/>{objectParameters}</object>', myTemplatesPattern )
            		} else {
            			this.template = new Template( '<embed {embedAttributes} allowfullscreen="true" />', myTemplatesPattern );
            		}
            	} else {
            		this.template = new Template(  'This content requires the Adobe Flash Player. '
            										   +' <a href=http://www.adobe.com/go/getflash/>Get Flash</a>', myTemplatesPattern );
            	}

            	this.parametersTemplate = new Template( '<param name="{name}" value="{value}" />', myTemplatesPattern );
            	this.attributesTemplate = new Template( ' {name}="{value}" ', myTemplatesPattern );
            },
			setAttribute : function( name, value ) {
				if(!this.applied) {
					this.attributes[name] = value;
                }
			},
			getAttribute : function( name ) {
				return this.attributes[name];
			},
			setAttributes : function( attributesList ) {
				$H(attributesList).each(function(pair){
					this.setAttribute(pair.key, pair.value);
				}.bind(this));
			},
			getAttributes : function( ) {
				return this.attributes;
			},
			apply : function( container ) {
				if (!this.applied)	{
					this.setAttribute( "id", Flex.uniqId());
					this.preInitBridge();
					var readyHTML = this.template.evaluate( this.generateTemplateValues() );
                    $(container).update( readyHTML );
				}
				this.applied = true;
			},
            applyWrite : function( ) {
				if (!this.applied)	{
					this.setAttribute( "id", Flex.uniqId());
					this.preInitBridge();
					var readyHTML = this.template.evaluate( this.generateTemplateValues() );
                    document.write( readyHTML );
				}
				this.applied = true;
			},
			preInitBridge: function () {
			    this.bridgeName = this.getAttribute('id') + 'bridge';
			    this.setAttribute('flashVars', 'bridgeName=' + this.bridgeName);
			    var scopeObj = this;
			    FABridge.addInitializationCallback(
			         this.bridgeName,
			         function () {
			             scopeObj.bridge = this.root();
			             scopeObj.initBridge();
			         }
			    );
			},
			initBridge: function() {
			    if(this.onBridgeInit) {
			        this.onBridgeInit(this.getBridge());
			    }
			},
            getBridge : function() {
				return this.bridge;
			},
			generateTemplateValues : function( )
			{
				var embedAttributes = {};
				var objectAttributes = {};
				var parameters = {};
				$H(this.attributes).each(function(pair) {
					var attributeName = pair.key.toLowerCase();
                    this.attributes[pair.key] = this.escapeAttributes( pair.value );
					switch (attributeName) {
						case "pluginspage":
							embedAttributes[pair.key] = this.attributes[pair.key];
							break;
						case "src":
						case "movie":
							embedAttributes['src'] = parameters['movie'] = this.attributes[pair.key];
							break;
						case "type":
							embedAttributes[pair.key]  = this.attributes[pair.key];
						case "classid":
						case "codebase":
							objectAttributes[pair.key] = this.attributes[pair.key];
							break;
						case "id":
							embedAttributes['name'] = this.attributes[pair.key];
						case "width":
						case "height":
						case "align":
						case "vspace":
						case "hspace":
						case "class":
						case "title":
						case "accesskey":
						case "name":
						case "tabindex":
							embedAttributes[pair.key] = objectAttributes[pair.key] = this.attributes[pair.key];
							break;
						default:
							embedAttributes[pair.key] = parameters[pair.key] = this.attributes[pair.key];
							break;
					}
				}.bind(this));

				var result = {
				    objectAttributes: '',
				    objectParameters: '',
				    embedAttributes : ''
				};


				$H(objectAttributes).each(function(pair){
			         result.objectAttributes += this.attributesTemplate.evaluate({
                         name:pair.key,
                         value:pair.value
			         });
				}.bind(this));

				$H(embedAttributes).each(function(pair){
			         result.embedAttributes += this.attributesTemplate.evaluate({
                         name:pair.key,
                         value:pair.value
			         });
				}.bind(this));

				$H(parameters).each(function(pair){
			         result.objectParameters += this.parametersTemplate.evaluate({
                         name:pair.key,
                         value:pair.value
			         });
				}.bind(this));

				return result;
			},
            escapeAttributes: function (value) {
                if(typeof value == 'string') {
                    return value.replace(new RegExp("&","g"), "&amp;");
                } else {
                    return value;
                }
            },
			detectFlashVersion : function( reqMajorVer, reqMinorVer, reqRevision ) {
				var versionStr = this.getSwfVer();
			    if (versionStr == -1 ) {
			        return false;
			    } else if (versionStr != 0) {
			        if(this.isIE && this.isWin && !this.isOpera) {
			            // Given "WIN 2,0,0,11"
			            tempArray         = versionStr.split(" ");  // ["WIN", "2,0,0,11"]
			            tempString        = tempArray[1];           // "2,0,0,11"
			            versionArray      = tempString.split(",");  // ['2', '0', '0', '11']
			        } else {
			            versionArray      = versionStr.split(".");
			        }
			        var versionMajor      = versionArray[0];
			        var versionMinor      = versionArray[1];
			        var versionRevision   = versionArray[2];

			            // is the major.revision >= requested major.revision AND the minor version >= requested minor
			        if (versionMajor > parseFloat(reqMajorVer)) {
			            return true;
			        } else if (versionMajor == parseFloat(reqMajorVer)) {
			            if (versionMinor > parseFloat(reqMinorVer))
			                return true;
			            else if (versionMinor == parseFloat(reqMinorVer)) {
			                if (versionRevision >= parseFloat(reqRevision))
			                    return true;
			            }
			        }
			        return false;
			    }
			},
			controlVersion : function () {
			    var version;
			    var axo;
			    var e;
			    try {
			        // version will be set for 7.X or greater players
			        axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
			        version = axo.GetVariable("$version");
			    } catch (e) {
			    }

			    if (!version) {
			        try {
			            axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");
			            version = "WIN 6,0,21,0";
			            axo.AllowScriptAccess = "always";
			            version = axo.GetVariable("$version");

			        } catch (e) {
			        }
			    }

			    if (!version) {
			        try {
			            axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
			            version = axo.GetVariable("$version");
			        } catch (e) {
			        }
			    }

			    if (!version) {
			        try {
			            axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
			            version = "WIN 3,0,18,0";
			        } catch (e) {
			        }
			    }

			    if (!version) {
			        try {
			            axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
			            version = "WIN 2,0,0,11";
			        } catch (e) {
			            version = -1;
			        }
			    }
			    return version;
			},
			getSwfVer : function (){
			    var flashVer = -1;
			    if (navigator.plugins != null && navigator.plugins.length > 0) {
			        if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
			            var swVer2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
			            var flashDescription = navigator.plugins["Shockwave Flash" + swVer2].description;
			            var descArray = flashDescription.split(" ");
			            var tempArrayMajor = descArray[2].split(".");
			            var versionMajor = tempArrayMajor[0];
			            var versionMinor = tempArrayMajor[1];
			            if ( descArray[3] != "" ) {
			                tempArrayMinor = descArray[3].split("r");
			            } else {
			                tempArrayMinor = descArray[4].split("r");
			            }
			            var versionRevision = tempArrayMinor[1] > 0 ? tempArrayMinor[1] : 0;
			            var flashVer = versionMajor + "." + versionMinor + "." + versionRevision;
			        }
			    }
			    else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != -1) flashVer = 4;
			    else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != -1) flashVer = 3;
			    else if (navigator.userAgent.toLowerCase().indexOf("webtv") != -1) flashVer = 2;
			    else if ( this.isIE && this.isWin && !this.isOpera ) {
			        flashVer = this.controlVersion();
			    }
			    return flashVer;
			}
} );

