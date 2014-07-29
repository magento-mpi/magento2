/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
require.config({
    "shim": {
        "jquery/bootstrap-carousel/jquery.bootstrap-carousel": ["jquery"], // no longer used
        "jquery/bootstrap-carousel/jquery.bootstrap-transition": ["jquery"], // no longer used
        "jquery/jquery.hashchange": ["jquery"],
        "jquery/jquery.mousewheel": ["jquery"],
        "jquery/jquery.popups": ["jquery"],
        "jquery/jquery-migrate": ["jquery"],
        "jquery/jstree/jquery.hotkeys": ["jquery"],
        "jquery/hover-intent": ["jquery"],
        "mage/adminhtml/backup": ["prototype"],
        "mage/adminhtml/tools": ["prototype"],
        "mage/adminhtml/varienLoader": ["prototype"],
        "mage/captcha": ["prototype"],
        "mage/common": ["jquery"],
        "mage/jquery-no-conflict": ["jquery"],
        "mage/requirejs/plugin/id-normalizer": ["jquery"],
        "mage/webapi": ["jquery"],
        "angular": {
            exports: 'angular'
        }
    },
    "paths":{
        "baseImage": 'Magento_Catalog/catalog/base-image-uploader',
        "jquery/validate": "jquery/jquery.validate",
        "jquery/hover-intent": "jquery/jquery.hoverIntent",
        "jquery/template": "jquery/jquery.tmpl.min",
        "jquery/farbtastic": "jquery/farbtastic/jquery.farbtastic",
        "jquery/file-uploader": "jquery/fileUploader/jquery.fileupload-ui",
        "handlebars": "jquery/handlebars/handlebars-v1.3.0",
        "jquery/jquery.hashchange": "jquery/jquery.ba-hashchange.min",
        "prototype": "prototype/prototype-amd",
        "_": "underscore",
        "angular": "angular/angular"
    },
    "deps": [
        "bootstrap"
    ]
});

require([
    'jquery',
    'mage/components',
    'mage/mage'
], function($, components){
    $.mage.component( components );
});
