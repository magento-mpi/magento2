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

var Product = {};

Product.Gallery = Class.create();
Product.Gallery.prototype = {
    images: [],
    file2id: {'no_selection':0},
    idIncrement: 1,
    containerId: '',
    container: null,
    uploader: null,
    imageTypes: {},
    initialize: function (containerId, uploader, imageTypes) {
        this.containerId  = containerId,
        this.container = $(this.containerId);
        this.uploader = uploader;
        this.imageTypes = imageTypes;
        this.uploader.onFilesComplete = this.handleUploadComplete.bind(this);
        this.uploader.onFileProgress  = this.handleUploadProgress.bind(this);
        this.uploader.onFileError     = this.handleUploadError.bind(this);
        this.images = this.getElement('save').value.evalJSON();
        this.imagesValues = this.getElement('save_image').value.evalJSON();
        this.template = new Template('<tr id="__id__" class="preview">' + this.getElement('template').innerHTML + '</tr>', /(^|.|\r|\n)(__([a-zA-Z0-9_]+)__)/);
        this.updateImages();
    },
    getElement:           function (name) {
        return $(this.containerId + '_' + name);
    },
    showUploader:         function () {
        this.getElement('add_images_button').hide();
        this.getElement('uploader').show();
    },
    handleUploadComplete: function (files) {
        Object(files).each(function(item) {

           if (!item.response.isJSON()) {
               throw $continue;
           }
           var response = item.response.evalJSON();
           if (response.error) {
               throw $continue;
           }
           var newImage = {};
           newImage.url = response.url;
           newImage.file = response.file;
           newImage.label = '';
           newImage.position = this.getNextPosition();
           newImage.disabled = 0;
           newImage.remove = 0;
           this.images.push(newImage);
           this.uploader.removeFile(item.id);
        }.bind(this));
        this.updateImages();
    },
    updateImages: function() {
        this.getElement('save').value = this.images.toJSON();
        this.images.each(function(row){
            if (!$(this.prepareId(row.file))) {
                this.createImageRow(row);
            }
            this.updateVisualisation(row.file);
        }.bind(this));
        this.updateUseDefault();
    },
    createImageRow: function(image) {
        var vars = Object.clone(image);
        vars.id = this.prepareId(image.file);
        var html = this.template.evaluate(vars);
        new Insertion.Bottom(this.getElement('list'), html);
    },
    prepareId: function(file) {
        if(typeof this.file2id[file] == 'undefined') {
            this.file2id[file] = this.idIncrement++;
        }
        return this.containerId + '-image-' + this.file2id[file];
    },
    getNextPosition: function() {
      var maxPosition = 0;
      this.images.each(function (item) {
         if (parseInt(item.position) > maxPosition) {
             maxPosition = parseInt(item.position);
         }
      });
      return maxPosition + 1;
    },
    updateImage: function(file) {
      var image = this.getImageByFile(file);
      image.label = this.getFileElement(file, 'cell-label input').value;
      image.position = this.getFileElement(file, 'cell-position input').value;
      image.remove = (this.getFileElement(file, 'cell-remove input').checked ? 1 : 0);
      image.disabled = (this.getFileElement(file, 'cell-disable input').checked ? 1 : 0);
      this.getElement('save').value = this.images.toJSON();
      this.updateState(file);
    },
    loadImage: function(file) {
      var image = this.getImageByFile(file);
      this.getFileElement(file, 'cell-image img').src = image.url;
      this.getFileElement(file, 'cell-image img').show();
      this.getFileElement(file, 'cell-image .place-holder').hide();
    },
    setProductImages: function(file) {
      $H(this.imageTypes).each(function(pair){
          if(this.getFileElement(file, 'cell-' + pair.key + ' input').checked) {
              this.imagesValues[pair.key] = (file == 'no_selection' ? null : file);
          }
      }.bind(this));

      this.getElement('save_image').value = $H(this.imagesValues).toJSON();
    },
    updateVisualisation: function(file) {
      var image = this.getImageByFile(file);
      this.getFileElement(file, 'cell-label input').value = image.label;
      this.getFileElement(file, 'cell-position input').value = image.position;
      this.getFileElement(file, 'cell-remove input').checked = (image.remove == 1);
      this.getFileElement(file, 'cell-disable input').checked = (image.disabled == 1);
      $H(this.imageTypes).each(function(pair) {
          if(this.imagesValues[pair.key] == file) {
              this.getFileElement(file, 'cell-' + pair.key + ' input').checked = true;
          }
      }.bind(this));
      this.updateState(file);
    },
    updateState: function (file) {
      if(this.getFileElement(file, 'cell-disable input').checked) {
          this.getFileElement(file, 'cell-position input').disabled = true;
      } else {
          this.getFileElement(file, 'cell-position input').disabled = false;
      }
    },
    getFileElement: function(file, element){
       if(!$$('#' + this.prepareId(file) + ' .' + element)[0]) {
           alert('#' + this.prepareId(file) + ' .' + element);
       }

       return $$('#' + this.prepareId(file) + ' .' + element)[0];
    },
    getImageByFile: function(file) {
      var image = false;
      this.images.each(function (item) {
         if (item.file == file) {
             image = item;
         }
      });
      return image;
    },
    updateUseDefault: function ()
    {
      if (this.getElement('default')) {
         this.getElement('default').getElementsBySelector('input').each(function(input){
             $(this.containerId).getElementsBySelector('.cell-' + input.value + ' input').each(function(radio) {
                 radio.disabled = input.checked;
             });
         }.bind(this));
      }
    },
    handleUploadProgress: function (file) {

    },
    handleUploadError:    function (fileId) {

    }
};