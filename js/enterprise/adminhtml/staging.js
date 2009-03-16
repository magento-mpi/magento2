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

if (!window.Enterprise) {
	window.Enterprise = {};
}
Enterprise.Staging = {};

Enterprise.Staging.Mapper = new Class.create();
Enterprise.Staging.Mapper.prototype = {
    templatePattern		: /(^|.|\r|\n)(\{\{(.*?)\}\})/,
    websiteRowTemplate	: null,
    storeRowTemplate	: null,
    storeAddBtnTemplate	: null,
    websiteIncrement	: 0,
    mapKeys				: null,
    addWebsiteMapRow	: null,
    initialize : function(containerId, url, pageVar, sortVar, dirVar, filterVar, mergeForm, stores)
    {
        this.mergeForm = $(mergeForm);
        this.containerId            = containerId;
        this.tableContainerId       = this.containerId + '_table';
        this.tableRowsContainerId   = this.containerId + '_rows';

        this.container              = $(this.containerId);
        this.tableContainer         = $(this.tableContainerId);
        this.tableRowsContainer     = $(this.tableRowsContainerId);

        this.stores = new $H(stores);

        this.grid = new varienGrid(containerId, url, pageVar, sortVar, dirVar, filterVar);

        this.mapKeys  = new $H();

        this.websiteRowTemplate     = new Template(
            this.getInnerElement('website_template').innerHTML,
            this.templatePattern
        );
        this.storeRowTemplate = new Template(
            this.getInnerElement('store_template').innerHTML,
            this.templatePattern
        );
        this.storeAddBtnTemplate = new Template(
            this.getInnerElement('store_add_btn_template').innerHTML,
            this.templatePattern
        );

        this.websiteIncrement = 0;

        this.addWebsiteMapRow = this.tableContainer.select('.staging-mapper-add-website-row')[0];
        var btn = this.addWebsiteMapRow.select('.staging-mapper-add-website-btn')[0];
        this.addWebsiteMapRow.btn = btn;
    },
    getInnerElement: function(elementName) {
        return $(this.containerId + '_' + elementName);
    },
    stagingWebsiteMapperRowInit : function(grid, row)
    {
        $(row).select('select').each(function(element) {
            element.parentRow = row;
            element.grid = grid;
            element.mapper = this;
            element.tableContainer = this.tableContainer;
            Event.observe(element, 'change', selectWebsiteMap);
        }.bind(this));
    },
    addWebsiteMap : function()
    {
        try {
            Element.insert(this.tableRowsContainer, {bottom: this.websiteRowTemplate.evaluate(this.getWebsiteVars())});
            var websiteRow = this.tableRowsContainer.lastChild;
            websiteRow.id = 'website-map-' + this.websiteIncrement;
            websiteRow.incrementId = this.websiteIncrement;
            websiteRow.key = '';
            websiteRow.addWebsiteMapRow = this.addWebsiteMapRow;

            websiteRow.fromElement    = websiteRow.select('.staging-mapper-website-from')[0];
            websiteRow.toElement      = websiteRow.select('.staging-mapper-website-to')[0];

            this.stagingWebsiteMapperRowInit(this.grid, websiteRow);

            Element.insert(this.tableRowsContainer, {bottom: this.storeAddBtnTemplate.evaluate({})});
            var addStoreMapRow = this.tableRowsContainer.lastChild;
            addStoreMapRow.id = 'website-map-' + this.websiteIncrement + '-store-add-btn';
            addStoreMapRow.websiteIncrementId = this.websiteIncrement;
            addStoreMapRow.storeIncrementId = 0;
            var btn = addStoreMapRow.select('.staging-mapper-add-store-btn')[0];
            addStoreMapRow.btn = btn;

            addStoreMapRow.websiteRow = websiteRow;
            websiteRow.addStoreMapRow = addStoreMapRow;

            this.disableBtn(this.addWebsiteMapRow.btn);

            this.websiteIncrement++;
        } catch (Error) {
            console.log(Error);
        }

        return false;
    },
    getWebsiteVars : function()
    {
        return {};
    },
    removeWebsiteMap : function(btn)
    {
        try {
            var websiteRow = $(btn).up().up();

            // unset maps cache for removed website mapping
            this.unsetRowMap(websiteRow);
            // remove add store button before
            websiteRow.addStoreMapRow.remove();
            // remove all stores related to website before
            this.tableContainer.select('.' + websiteRow.id + '-store').each(function(row){this.unsetRowMap(row); row.remove();}.bind(this));
            // remove website row
            websiteRow.remove();

            this.enableBtn(websiteRow.addWebsiteMapRow.btn);
        } catch (Error) {
            console.log(Error);
        }

        return false;
    },
    unsetRowMap : function(row)
    {
        var key = row.key;
        this.mapKeys.unset(key);
    },
    enableBtn : function(btn)
    {
        btn.disabled = false;
        btn.removeClassName('disabled');
    },
    disableBtn : function(btn)
    {
        btn.disabled = true;
        btn.addClassName('disabled');
    },
    stagingStoreMapperRowInit : function(grid, row)
    {
        $(row).select('select').each(function(element) {
            element.parentRow = row;
            element.mapper = this;
            element.tableContainer = this.tableContainer;
            Event.observe(element, 'change', selectStoreMap);
        }.bind(this));
    },
    addStoreMap : function(btn)
    {
        try {
            var addNewRow = $(btn).up().up();

            Element.insert(addNewRow, {before: this.storeRowTemplate.evaluate(this.getStoreVars(addNewRow.websiteRow))});

            var storeRow = addNewRow.previousSibling;
            storeRow.id = 'website-map-' + addNewRow.websiteIncrementId + '-store-' + addNewRow.storeIncrementId;
            storeRow.addClassName('website-map-' + addNewRow.websiteIncrementId + '-store');
            storeRow.addNewRow = addNewRow;
            storeRow.key = '';

            storeRow.select('.staging-mapper-store-from').each(
                    function(fromElement)
                    {
                        fromElement.fromWebsite = addNewRow.websiteRow.fromWebsite;
                        storeRow.fromElement    = fromElement;
                    }
            );
            storeRow.select('.staging-mapper-store-to').each(
                    function(toElement)
                    {
                        toElement.toWebsite = addNewRow.websiteRow.toWebsite;
                        storeRow.toElement      = toElement;
                    }
            );

            this.stagingStoreMapperRowInit(this.grid, storeRow);

            addNewRow.storeIncrementId++;

            this.disableBtn(addNewRow.btn);
        } catch (Error) {
            console.log(Error);
        }

        return false;
    },
    getStoreVars : function(websiteRow)
    {
        return {
            store_from_select  : this.getStoreFromSelect(websiteRow),
            store_to_select    : this.getStoreToSelect(websiteRow)
        };
    },
    removeStoreMap : function(btn)
    {
        try {
            var row = $(btn).up().up();

            this.unsetRowMap(row);
            row.remove();

            this.enableBtn(row.addNewRow.btn);
        } catch (Error) {
            console.log(Error);
        }

        return false;
    },
    getStoreFromSelect : function(websiteRow)
    {
        var fromWebsite = websiteRow.fromWebsite;
        var toWebsite = websiteRow.toWebsite;
        var stores = this.stores.get(fromWebsite);
        if (typeof(stores) == 'undefined') {
            return '<span>No stores exists</span>' ;
        }
        var options = '<select name="map[stores]['+fromWebsite+'-'+toWebsite+'][from][]" class="staging-mapper-store-from">';
        options+= '<option value=""></option>';
        stores.each(function(store){
            options+= '<option value="'+store.store_id+'">'+store.name+'</option>';
        });
        options+= '</option>';
        options = Object.toHTML(options);
        return options;
    },
    getStoreToSelect : function(websiteRow)
    {
        var fromWebsite = websiteRow.fromWebsite;
        var toWebsite = websiteRow.toWebsite;
        var stores = this.stores.get(toWebsite);
        if (typeof(stores) == 'undefined') {
            return '<span>No stores exists</span>' ;
        }
        var options = '<select name="map[stores]['+fromWebsite+'-'+toWebsite+'][to][]" class="staging-mapper-store-to">';
        options+= '<option value=""></option>';
        stores.each(function(store){
            options+= '<option value="'+store.store_id+'">'+store.name+'</option>';
        });
        options+= '</option>';
        options = Object.toHTML(options);
        options.toWebsite = toWebsite;
        return options;
    },
    stagingMerge : function(grid)
    {
        alert('MERGING STARTS!');
        this.mergeForm.submit();
    }
};

checkUniqueMap = function(mapper, key)
{
    if (mapper.mapKeys.get(key)) {
        return 2;
    } else {
        return 1;
    }
};

selectWebsiteMap = function(event)
{
    var element = Event.element(event);
    element = $(element);
    if (!element.parentRow) {
       return;
    }
    if (!element.mapper) {
        return;
    }

    var fromElement = element.parentRow.fromElement;
    var toElement = element.parentRow.toElement;

    if (fromElement) {
        element.parentRow.fromWebsite = fromElement.value;
    }

    if (toElement) {
        element.parentRow.toWebsite = toElement.value;
    }

    var addStoreMapBtn   = element.parentRow.addStoreMapRow.btn;
    var addWebsiteMapBtn = element.parentRow.addWebsiteMapRow.btn;

    if (fromElement && toElement) {
        if (fromElement.value && toElement.value) {
            var key = 'websites' + '-' + fromElement.value + '-' + toElement.value;
            element.parentRow.key = key;
            var result = checkUniqueMap(element.mapper, key);
            if (result == 1) {
                $(element.parentRow).select('.mapper-status')[0].innerHTML = 'OK';
                element.mapper.mapKeys.set(key, true);

                fromElement.prevValue   = fromElement.value;
                toElement.prevValue     = toElement.value;

                element.mapper.enableBtn(addStoreMapBtn);
                element.mapper.enableBtn(addWebsiteMapBtn);
            } else if (result == 2) {
                alert('Please, try another combination.');
                fromElement.value = '';
                if (fromElement.prevValue) {
                    fromElement.value = fromElement.prevValue;
                }
                toElement.value = '';
                if (toElement.prevValue) {
                    toElement.value = toElement.prevValue;
                }
                element.mapper.disableBtn(addWebsiteMapBtn);
            }
        } else {
            $(element.parentRow).select('.mapper-status')[0].innerHTML = '';
            // remove all stores related to website
            var stores = this.tableContainer.select('.'+element.parentRow.id+'-store').each(function(row){row.remove();});
            var storeAddBtnRow = $(element.parentRow.id+'-store-add-btn');
            var btn = storeAddBtnRow.select('.staging-mapper-add-store-btn')[0];
            element.mapper.disableBtn(btn);
            if (fromElement.prevValue && toElement.prevValue) {
                var key = 'websites' + '-' + fromElement.prevValue + '-' + toElement.prevValue;
                element.mapper.mapKeys.unset(key);
            }
        }
    } else {
        $(element.parentRow).select('.mapper-status')[0].innerHTML = '';
    }
};
selectStoreMap = function(event)
{
    var element = Event.element(event);
    element = $(element);
    if (!element.parentRow) {
       return;
    }
    if (!element.mapper) {
        return;
    }

    var fromElement = element.parentRow.fromElement;
    if (fromElement) {
        element.parentRow.fromStore = fromElement.value;
    }
    var toElement = element.parentRow.toElement;
    if (toElement) {
        element.parentRow.toStore = toElement.value;
    }

    var btn = element.parentRow.addNewRow.btn;

    if (fromElement && toElement) {
        if (fromElement.value && toElement.value) {
            var key = 'stores' + '-' + fromElement.fromWebsite + '-' + fromElement.value + '-' + toElement.toWebsite + '-' + toElement.value;
            element.parentRow.key = key;
            var result = checkUniqueMap(element.mapper, key);
            if (result == 1) {
                $(element.parentRow).select('.mapper-status')[0].innerHTML = 'OK';
                element.mapper.mapKeys.set(key, true);

                fromElement.prevValue   = fromElement.value;
                toElement.prevValue     = toElement.value;

                element.mapper.enableBtn(btn);
            } else if (result == 2) {
                alert('Please, trye another combination.');
                fromElement.value = '';
                if (fromElement.prevValue) {
                    fromElement.value = fromElement.prevValue;
                }
                toElement.value = '';
                if (toElement.prevValue) {
                    toElement.value = toElement.prevValue;
                }
                element.mapper.disableBtn(btn);
            } else if (result == 3) {
                element.mapper.disableBtn(btn);
            }
        } else {
            $(element.parentRow).select('.mapper-status')[0].innerHTML = '';
            element.mapper.disableBtn(btn);
        }
    } else {
        $(element.parentRow).select('.mapper-status')[0].innerHTML = '';
    }
};

var stagingTemplateSyntax = /(^|.|\r|\n)({{(\w+)}})/;

function setSettings(urlTemplate, websiteElement, setElement, typeElement)
{
    var template = new Template(urlTemplate, stagingTemplateSyntax);
    setLocation(template.evaluate({websites:$F(websiteElement),set:$F(setElement),type:$F(typeElement)}));
}

function saveAndContinueEdit(urlTemplate)
{
    var template = new Template(urlTemplate, stagingTemplateSyntax);
    var url = template.evaluate({tab_id : enterprise_staging_tabsJsTabs.activeTab.id});
    stagingForm.submit(url);
}
