(function() {
    tinymce.create('tinymce.plugins.MagentowidgetPlugin', {
        /**
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addCommand('mceMagentowidget', function() {
                ed.windowManager.open({
                    file : ed.settings.magentowidget_url,
                    width : 1024,
                    height : 800,
                    inline : 1
                }, {
                    plugin_url : url // Plugin absolute URL
                });
            });

            // Register Varienimages button
            ed.addButton('magentowidget', {
                title : 'magentowidget.insert_image',
                cmd : 'mceMagentowidget',
                image : url + '/img/icon.gif'
            });

            // Add a node change handler, selects the button in the UI when a image is selected
//            ed.onNodeChange.add(function(ed, cm, n) {
//                cm.setActive('magentowidget', n.nodeName == 'IMG');
//            });
        },

        getInfo : function() {
            return {
                longname : 'Magento Widget Manager Plugin for TinyMCE 3.x',
                author : 'Magento Core Team',
                authorurl : 'http://magentocommerce.com',
                infourl : 'http://magentocommerce.com',
                version : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('magentowidget', tinymce.plugins.MagentowidgetPlugin);
})();
