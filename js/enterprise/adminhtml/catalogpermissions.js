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
 
 Enterprise.CatalogPermissions = {};
 
 Enterprise.CatalogPermissions.CategoryTab = Class.create();
 Object.extend(Enterprise.CatalogPermissions.CategoryTab.prototype, {
     templatesPattern: /(^|.|\r|\n)(\{\{(.*?)\}\})/,
     initialize: function (container, config) {
         this.container = $(container);
         this.config = config;
         this.permissions = $H((Object.isArray(this.config.permissions) ? {} : this.config.permissions));
         this.rowTemplate = new Template(this.config.row, this.templatesPattern);
         this.addButton = this.container.down('button.add');
         this.items = this.container.down('.items');
         this.onAddButton = this.handleAddButton.bindAsEventListener(this);
         this.onDeleteButton = this.handleDeleteButton.bindAsEventListener(this);
         this.onFieldChange = this.handleUpdatePermission.bindAsEventListener(this);
         this.addButton.observe('click', this.onAddButton);
         this.index = 1;
         Validation.addAllThese([
            ['validate-duplicate-' + this.container.id, this.config.duplicate_message, function(v, elem) {
                return !$(elem).isDuplicate;
            }]
         ]);
         this.permissions.each(this.add.bind(this));
    },
    add: function () {
        var config = {
            index: this.index++
        };
        config.html_id = this.container.id + '_row_' + config.index;
        var params, i, l;
        
        if (arguments.length) {
            Object.extend(config, arguments[0].value);
            params = Object.keys(config);
            for (i=0, l=params.length; i < l; i ++) {
               if (params[i].match(/grant_/i)) {
                   // Workaround for IE
                   config[params[i] + '_' + config[params[i]]] = 'checked="checked"';
               }
            }
            params.push('id');
            config.id = config.permission_id;
            config.permission_id = arguments[0].key;
        } else {
            config.permission_id = 'new_permission' + config.index;
            params = Object.keys(config);   
            this.permissions.set(config.permission_id, {});
        }
        
        this.items.insert({bottom: this.rowTemplate.evaluate(config)});
        
        var row  = $(config.html_id);
        row.permissionId = config.permission_id;
        row.controller = this;
        
        
        
        for (i=0, l=params.length; i < l; i ++) {
            if (row.down('.' + this.fieldClassName(params[i]))) {
               if (!params[i].match(/grant_/i)) {
                    row.down('.' + this.fieldClassName(params[i])).value = config[params[i]];
               }
            }
        }
        
        if (arguments.length == 0) {
             row.select('input[value="0"]').each(function(radio){
                 if (radio.type == 'radio') {
                     radio.checked = true;
                 }
             });
        }
        
        
        var fields = row.select('input', 'select', 'textarea');
        for (i = 0, l = fields.length; i < l; i ++) {
            fields[i].observe('change', this.onFieldChange);
            if (fields[i].hasClassName('permission-duplicate')) {
                row.duplicateField = fields[i];
                row.duplicateField.isDuplicate = false;
                row.duplicateField.addClassName('validate-duplicate-' + this.container.id);
            }
        }
        
        row.down('button.delete').observe('click', this.onDeleteButton);
    },
    
    handleAddButton: function (evt) {
        this.add();
    },
    handleUpdatePermission: function (evt) {
        var field = $(Event.element(evt));
        var row = field.up('.permission-box');
        
        if (field.name && (field.type != 'radio' || field.checked)) {
            var fieldName = field.name.replace(/^(.*)\[([^\]])\]$/, '$2');
            this.permissions.get(row.permissionId)[fieldName] = field.value;
        }
        
        if (field.hasClassName('is-unique')) {
            this.checkDuplicates();
            this.validate();
        }
    },
    isDuplicate: function(row) {
        var needleString = this.rowUniqueKey(row);
        
        if (needleString.length == 0 || row.isDeleted) {
            return false;
        }
        
        var rows = this.items.select('.permission-box');
        for (var i = 0, l = rows.length; i < l; i ++) {
            if (!rows[i].isDuplicate &&
                !rows[i].isDeleted && 
                rows[i].permissionId != row.permissionId && 
                this.rowUniqueKey(rows[i]) == needleString) {
                return true;
            }
        }
        
        return false;
    },
    checkDuplicates: function () {
        var rows = this.items.select('.permission-box');
        for (var i = 0, l = rows.length; i < l; i ++) {
            rows[i].duplicateField.isDuplicate = this.isDuplicate(rows[i]);
        }
    },
    rowUniqueKey: function (row) {
        var fields = row.select('select.is-unique', 'input.is-unique');
        var key = '';
        for (var i=0, l=fields.length; i < l; i ++) {
            if (fields[i].value !== '') {
                key += '_' + fields[i].value;
            }
        }
        
        return key;
    },
    fieldClassName: function(fieldName) {
        return fieldName.replace(/_/g, '-') + '-value';
    },
    handleDeleteButton: function (evt) {
        var button = $(Event.element(evt));
        var row = button.up('.permission-box');
        row.isDeleted = true;
        row.down('.' + this.fieldClassName('_deleted')).value = '1';
        row.addClassName('no-display');
        this.checkDuplicates();
        this.validate();
    },
    validate: function () {
        if (arguments.length > 0) {
            Validation.validate(arguments[0]);
            return;
        }
        var fields = this.container.select('input.permission-duplicate');
        for (var i=0, l=fields.length; i < l; i++) {
            Validation.validate(fields[i]);
        }
    }
 })