<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Variable Wysiwyg Plugin Config
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Variable;

class Config
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_url;

    /**
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Backend\Model\UrlInterface $url
     */
    public function __construct(\Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Backend\Model\UrlInterface $url)
    {
        $this->_assetRepo = $assetRepo;
        $this->_url = $url;
    }

    /**
     * Prepare variable wysiwyg config
     *
     * @param \Magento\Framework\Object $config
     * @return array
     */
    public function getWysiwygPluginSettings($config)
    {
        $variableConfig = array();
        $onclickParts = array(
            'search' => array('html_id'),
            'subject' => 'MagentovariablePlugin.loadChooser(\'' .
            $this->getVariablesWysiwygActionUrl() .
            '\', \'{{html_id}}\');'
        );
        $variableWysiwyg = array(
            array(
                'name' => 'magentovariable',
                'src' => $this->getWysiwygJsPluginSrc(),
                'options' => array(
                    'title' => __('Insert Variable...'),
                    'url' => $this->getVariablesWysiwygActionUrl(),
                    'onclick' => $onclickParts,
                    'class' => 'add-variable plugin'
                )
            )
        );
        $configPlugins = $config->getData('plugins');
        $variableConfig['plugins'] = array_merge($configPlugins, $variableWysiwyg);
        return $variableConfig;
    }

    /**
     * Return url to wysiwyg plugin
     *
     * @return string
     */
    public function getWysiwygJsPluginSrc()
    {
        $editorPluginJs = 'mage/adminhtml/wysiwyg/tiny_mce/plugins/magentovariable/editor_plugin.js';
        return $this->_assetRepo->getUrl($editorPluginJs);
    }

    /**
     * Return url of action to get variables
     *
     * @return string
     */
    public function getVariablesWysiwygActionUrl()
    {
        return $this->_url->getUrl('adminhtml/system_variable/wysiwygPlugin');
    }
}
