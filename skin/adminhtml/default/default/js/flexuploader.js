/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

if(!window.Flex) {
    alert('Flex library not loaded');
} else {
    Flex.Uploader = Class.create();
    Flex.Uploader.prototype = {
        flex: null,
        uploader:null,
        filters:null,
        containerId:null,
        flexContainerId:null,
        container:null,
        files:null,
        fileRowTemplate:null,
        fileProgressTemplate:null,
        templatesPattern: /(^|.|\r|\n)(\{\{(.*?)\}\})/,
        initialize: function(containerId, uploaderSrc, config) {
            this.containerId = containerId;
            this.container   = $(containerId);

            this.container.controller = this;

            this.config = config;
            this.flexContainerId = this.containerId + '-flash';
            new Insertion.Top(
                this.container,
                '<div id="'+this.flexContainerId+'" class="right"></div>'
            );

            this.flex = new Flex.Object({
                width:  1,
                height: 1,
                src:    uploaderSrc,
                wmode: 'transparent'
            });
            this.getInnerElement('browse').disabled = true;

            this.fileRowTemplate = new Template(
                this.getInnerElement('template').innerHTML,
                this.templatesPattern
            );

            this.fileProgressTemplate = new Template(
                this.getInnerElement('template-progress').innerHTML,
                this.templatesPattern
            );

            this.flex.onBridgeInit = this.handleBridgeInit.bind(this);
            this.flex.apply(this.flexContainerId);
            this.getInnerElement('upload').hide();
        },
        getInnerElement: function(elementName) {
            return $(this.containerId + '-' + elementName);
        },
        getFileId: function(file) {
            return this.containerId + '-file-' + file.id;
        },
        handleBridgeInit: function() {
            this.uploader = this.flex.getBridge().getUpload();
            if(this.config.filters) {
                $H(this.config.filters).each(function(pair) {
                    this.uploader.addTypeFilter(pair.key, pair.value.label, pair.value.files);
                }.bind(this));
                delete(this.config.filters);
                this.uploader.setUseTypeFilter(true);
            }
            this.uploader.setConfig(this.config);
            this.uploader.addEventListener('select',    this.handleSelect.bind(this));
            this.uploader.addEventListener('complete',  this.handleComplete.bind(this));
            this.uploader.addEventListener('progress',  this.handleProgress.bind(this));
            this.uploader.addEventListener('error',     this.handleError.bind(this));
            this.getInnerElement('browse').disabled = false;
        },
        browse: function() {
            this.uploader.browse();
        },
        upload: function() {
            this.uploader.upload();
        },
        handleSelect: function (event) {
            this.files = event.getData().files;
            this.updateFiles();
            this.getInnerElement('upload').show();
        },
        handleProgress: function (event) {
            this.updateFile(event.getData().file);
        },
        handleError: function (event) {
            this.updateFile(event.getData().file);
        },
        handleComplete: function (event) {
            this.files = event.getData().files;
            this.updateFiles();
        },
        updateFiles: function () {
            this.files.each(function(file) {
                this.updateFile(file);
            }.bind(this));
        },
        updateFile:  function (file) {
            if(!$(this.getFileId(file))) {
                new Insertion.Bottom(
                    this.container,
                    this.fileRowTemplate.evaluate(this.getFileVars(file))
                );
            }
            var progress = $(this.getFileId(file)).getElementsByClassName('progress-text')[0];
            if((file.status=='progress') || (file.status=='complete')) {
                $(this.getFileId(file)).addClassName('progress');
                $(this.getFileId(file)).removeClassName('new');
                progress.update(this.fileProgressTemplate.evaluate(this.getFileProgressVars(file)));
            } else if(file.status=='error') {
                $(this.getFileId(file)).addClassName('error');
                $(this.getFileId(file)).removeClassName('progress');
                $(this.getFileId(file)).removeClassName('new');
                progress.update('Error: ' + file.error + ' HTTP '+ file.http);
            } else if(file.status=='full_complete') {
                $(this.getFileId(file)).addClassName('complete');
                $(this.getFileId(file)).removeClassName('progress');
            }

        },
        getFileVars: function(file) {
            return {
                id   : this.getFileId(file),
                name : file.name,
                size : this.formatSize(file.size)
            };
        },
        getFileProgressVars: function(file) {
            return {
                total    : this.formatSize(file.progress.total),
                uploaded : this.formatSize(file.progress.loaded),
                percent  : this.round((file.progress.loaded/file.progress.total)*100)
            };
        },
        formatSize: function(size) {
            if (size > 1024*1024*1024*1024) {
                return this.round(size/(1024*1024*1024*1024)) + ' TB';
            } else if (size > 1024*1024*1024) {
                return this.round(size/(1024*1024*1024)) + ' GB';
            } else if (size > 1024*1024) {
                return this.round(size/(1024*1024)) + ' MB';
            } else if (size > 1024) {
                return this.round(size/(1024)) + ' KB';
            }
            return size + ' B';
        },
        round: function(number) {
            return Math.round(number*100)/100;
        }
    }
}