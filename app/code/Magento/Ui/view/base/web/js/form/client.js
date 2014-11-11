/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
define([
    'jquery',
    'underscore',
    'mage/utils',
    'Magento_Ui/js/lib/class'
], function($, _, utils, Class){
    'use strict';
    
    var defaults = {};

    function beforeSave(data, url){
        var save        = $.Deferred(),
            serialized  = utils.serialize(data);
        
        serialized.form_key = FORM_KEY;
        
        if(!url){
            save.resolve(data);
        }

        $('body').trigger('processStart');

        $.ajax({
            url: url,
            data: serialized,
            success: function(resp){
                if(!resp.error){
                    save.resolve(data);
                }
            },
            complete: function(){
                $('body').trigger('processStop');
            }
        });

        return save.promise();
    }

    return Class.extend({
        /**
         * Initializes DataProvider instance.
         * @param {Object} settings - Settings to initialize object with.
         */
        initialize: function(config) {
            _.extend(this, defaults, config);

            _.bindAll(this, '_save');
        },

        /**
         * Assembles data and submits it using 'utils.submit' method
         */
        save: function(data){
            var url = this.urls.beforeSave;

            beforeSave(data, url).then(this._save);

            return this;
        },

        _save: function(data){
            data.form_key = FORM_KEY;

            utils.submit({
                url:    this.urls.save,
                data:   data
            });

            return this;
        }
    });
});