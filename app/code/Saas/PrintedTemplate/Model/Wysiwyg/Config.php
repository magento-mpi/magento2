<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Wysiwyg Config for Editor HTML Element
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Models
 */
class Saas_PrintedTemplate_Model_Wysiwyg_Config extends Mage_Cms_Model_Wysiwyg_Config
{
    /**
     * Default value for wysiwyg editor available fonts
     *
     * @var string
     */
    protected $_defaultFonts;

    /**
     * Initializes config model
     *
     * @param Mage_Core_Model_View_Url $viewUrl
     */
    public function __construct(Mage_Core_Model_View_Url $viewUrl)
    {
        parent::__construct($viewUrl, array(
            'buttons_to_remove' => 'media',
            'footer_separator' => '<!-- FOOTER SEPARATOR -->',
            'header_separator' => '<!-- HEADER SEPARATOR -->',
            'header_error_message' => Mage::helper('Saas_PrintedTemplate_Helper_Data')
                ->__('The template can contain only one header separator.'),
            'footer_error_message' => Mage::helper('Saas_PrintedTemplate_Helper_Data')
                ->__('The template can contain only one footer separator.'),
            'page_break_separator' =>
                '<div style="height: 0; clear: both; page-break-after: always;"><!-- pagebreak --></div>',
        ));
    }

    /**
     * Return Wysiwyg config as Magento_Object
     *
     * Config options description:
     *
     * enabled:                 Enabled Visual Editor or not
     * hidden:                  Show Visual Editor on page load or not
     * use_container:           Wrap Editor contents into div or not
     * no_display:              Hide Editor container or not (related to use_container)
     * translator:              Helper to translate phrases in lib
     * files_browser_*:         Files Browser (media, images) settings
     * encode_directives:       Encode template directives with JS or not
     *
     * @param $data array constructor params to override default config values
     * @return Magento_Object
     */
    public function getConfig($data = array())
    {
        if (!isset($data['skip_printed_template_widgets'])) {
            $data['skip_printed_template_widgets'] = false;
        }
        $config = parent::getConfig($data);
        $config->setTranslator(Mage::helper('Saas_PrintedTemplate_Helper_Data'));

        $config->addData($this->getVariablesPlugin());

        return $config;
    }

    /**
     * Prepares config for variables plugin
     *
     * @return array
     */
    protected function getVariablesPlugin()
    {
        $variableConfig = array();
        $onclickParts = array(
            'search' => array('html_id'),
            'subject' => 'MagentovariablePlugin.loadChooser(\''
                . $this->getVariablesWysiwygActionUrl()
                . '\', \'{{html_id}}\');'
        );
        $wysiwygPlugin = array(array(
            'name' => 'magentovariable',
            'src' => $this->getWysiwygJsPluginSrc(),
            'options' => array(
                'title' => Mage::helper('Saas_PrintedTemplate_Helper_Data')->__('Insert Variable...'),
                'url' => $this->getVariablesWysiwygActionUrl(),
                'onclick' => $onclickParts,
                'class'   => 'add-variable plugin'
        )));
        $variableConfig['plugins'] = array_merge($wysiwygPlugin, $this->getHeaderFooterPlugin());

        return $variableConfig;
    }

    /**
     * Get settings for Headerfooter plugin
     *
     * @return array
     */
    protected function getHeaderFooterPlugin()
    {
        $viewUrl = $this->_viewUrl;

        $viewUrl->getViewFileUrl(
            'Saas_PrintedTemplate::wysiwyg/tiny_mce/plugins/magentoheaderfooter/css/content.css'
        );
        $viewUrl->getViewFileUrl(
            'Saas_PrintedTemplate::wysiwyg/tiny_mce/plugins/magentoheaderfooter/img/footer-icon.gif'
        );
        $viewUrl->getViewFileUrl(
            'Saas_PrintedTemplate::wysiwyg/tiny_mce/plugins/magentoheaderfooter/img/header-icon.gif'
        );
        $viewUrl->getViewFileUrl(
            'Saas_PrintedTemplate::wysiwyg/tiny_mce/plugins/magentoheaderfooter/img/trans.gif'
        );
        return array(
            array('name' => 'magentofooter',
                'src' => $viewUrl->getViewFileUrl(
                    'Saas_PrintedTemplate::wysiwyg/tiny_mce/plugins/magentoheaderfooter/editor_plugin.js'
                ),
                'options' => array(
                    'title'   => Mage::helper('Saas_PrintedTemplate_Helper_Data')->__('Insert Footer Separator'),
                    'onclick' => "tinymce.plugins.MagentoheaderfooterPlugin.insertSeparatorToTextarea(
                        'printedtemplate_content',
                        '{$this->getFooterSeparator()}',
                        '{$this->getFooterErrorMessage()}');",
                    'class'   => 'plugin'
                ),
            ),
            array('name' => 'magentoheader',
                'src' => $viewUrl->getViewFileUrl(
                    'Saas_PrintedTemplate::wysiwyg/tiny_mce/plugins/magentoheaderfooter/editor_plugin.js'
                ),
                'options' => array(
                    'title'   => Mage::helper('Saas_PrintedTemplate_Helper_Data')->__('Insert Header Separator'),
                    'onclick' => "tinymce.plugins.MagentoheaderfooterPlugin.insertSeparatorToTextarea(
                        'printedtemplate_content',
                        '{$this->getHeaderSeparator()}',
                        '{$this->getHeaderErrorMessage()}');",
                    'class'   => 'plugin'
                ),
            ),
        );
    }

    /**
     * Returns url for wysiwyg variables action
     *
     * @return string
     */
    protected function getVariablesWysiwygActionUrl()
    {
        return Mage::getSingleton('Mage_Backend_Model_Url')->getUrl('*/template/wysiwygVariables');
    }

    /**
     * Returns url to js for wysiwyg
     *
     * @return string
     */
    protected function getWysiwygJsPluginSrc()
    {
        $editorPluginJs = 'mage/adminhtml/wysiwyg/tiny_mce/plugins/magentovariable/editor_plugin.js';
        return $this->_viewUrl->getViewFileUrl($editorPluginJs);
    }

    /**
     * Returns font configuration for WYSIWYG editor
     *
     * @return string
     */
    public function getFonts()
    {
        if (!$this->hasData('fonts')) {
            $fonts = '';
            $config = Mage::getSingleton('Saas_PrintedTemplate_Model_Config')->getFontsArray();
            foreach ($config as $font) {
                $fonts .= $font['label'] . '=' . $font['css'] . ';';
            }
            $this->setData('fonts', rtrim($fonts, ';'));
        }

        return $this->getData('fonts');
    }
}
