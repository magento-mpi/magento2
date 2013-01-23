/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

tinyMCE.addI18n({en:{
    magentoheaderfooter:
    {
        insert_header : "Insert Header Separator",
        insert_footer : "Insert Footer Separator"
    }
}});

(function() {
    tinymce.create('tinymce.plugins.MagentoheaderfooterPlugin', {
        /**
         * @param {tinymce.Editor} editor Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(editor, url)
        {
            this.initControl('header', editor, url);
            this.initControl('footer', editor, url);
        },

        /**
         * Inititialize buttons to add header and footer
         *
         * @param {string} type Type of button either 'footer' of 'header'.
         * @param {tinymce.Editor} editor Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        initControl: function(type, editor, url)
        {
            var separatorName = command = 'mceMagento' + type;
            var separatorHtml = '<img id="' + separatorName + '" src="' + url + '/img/trans.gif" class="' + separatorName + ' mceItemNoResize"/>';
            var separator = editor.getParam('magento' + type + '_separator', '<!-- magento' + type + ' -->');
            var errorMessage = editor.getParam('magento' + type + '_error_message', 'The template can contain only one ' + type + '.');
            var filtredSeparator = new RegExp(
                separator.replace(/[\?\.\*\[\]\(\)\{\}\+\^\$\:]/g,function(a) {return '\\' + a;}), 'g'
            );

            // Register commands
            editor.addCommand(command, function() {
                if(editor.dom.doc.getElementById(separatorName)) {
                    alert(errorMessage);
                } else {
                    editor.execCommand('mceInsertContent', 0, separatorHtml);
                }
            });

            // Register buttons
            editor.addButton('magento' + type + '', {
                title : 'magentoheaderfooter.insert_' + type,
                cmd   : command,
                image : url + '/img/' + type + '-icon.gif'
            });

            editor.onInit.add(function() {
                if (editor.settings.content_css !== false) {
                    editor.dom.loadCSS(url + "/css/content.css");
                }

                if (editor.theme.onResolveName) {
                    editor.theme.onResolveName.add(function(th, o) {
                        if (o.node.nodeName == 'IMG' && editor.dom.hasClass(o.node, separatorName))
                            o.name = 'magento' + type;
                    });
                }
            });

            editor.onClick.add(function(editor, element) {
                element = element.target;
                if (element.nodeName === 'IMG' && editor.dom.hasClass(element, separatorName)) {
                    editor.selection.select(element);
                }
            });

            editor.onNodeChange.add(function(editor, cm, n) {
                cm.setActive('magento' + type, n.nodeName === 'IMG' && editor.dom.hasClass(n, separatorName));
            });

            editor.onBeforeSetContent.add(function(editor, o) {
                o.content = o.content.replace(filtredSeparator, separatorHtml);
            });

            editor.onPostProcess.add(function(editor, o) {
                if (o.get) {
                    o.content = o.content.replace(/<img[^>]+>/g, function(image) {
                        if (image.indexOf('class="' + separatorName) !== -1) {
                            image = separator;
                        }

                        return image;
                    });
                }
            });
        },

        /**
         * Get info about this plug-in
         */
        getInfo : function()
        {
            return {
                longname  : 'Magento HeaderFooter Plugin for TinyMCE 3.x',
                author    : 'Alex Kusakin',
                authorurl : 'http://magentocommerce.com',
                infourl   : 'http://magentocommerce.com',
                version   : "1.0"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('magentoheader', tinymce.plugins.MagentoheaderfooterPlugin);

    /**
     * Add header/footer separator in the textarea
     *
     * @param {string} textareaId ID of textarea element.
     * @param {string} type Type of button either 'footer' of 'header'.
     * @param {string} separator Seprartor string to insert.
     */
    tinymce.plugins.MagentoheaderfooterPlugin.insertSeparatorToTextarea = function(textareaId, separator, errorMessage)
    {
        var textareaElm = $(textareaId);
        if (!separator) {
            separator = '<!-- magento_separator -->';
        }

        if (textareaElm) {
            // Is the separator already exist in the textarea
            if (textareaElm.value.indexOf(separator) > -1) {
                if (errorMessage) {
                    alert(errorMessage);
                }
                return;
            }

            var scrollPos = textareaElm.scrollTop;
            updateElementAtCursor(textareaElm, separator);
            textareaElm.focus();
            textareaElm.scrollTop = scrollPos;
            textareaElm = null;
        }
    }
})();
