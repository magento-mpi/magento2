<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor;

/**
 * Editor toolbar
 *
 * @method \Magento\DesignEditor\Block\Adminhtml\Editor\Container setTheme(\Magento\View\Design\ThemeInterface $theme)
 */
class Container extends \Magento\Backend\Block\Widget\Container
{
    /**
     * Frame Url
     *
     * @var string
     */
    protected $_frameUrl;

    /**
     * Add elements in layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->addButton('back_button', array(
            'label'   => __('Back'),
            'onclick' => 'setLocation(\'' . $this->getUrl('adminhtml/*') . '\')',
            'class'   => 'back'
        ));

        parent::_prepareLayout();
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return __('Store Designer');
    }

    /**
     * @param string $url
     *
     * @return \Magento\DesignEditor\Block\Adminhtml\Editor\Container
     */
    public function setFrameUrl($url)
    {
        $this->_frameUrl = $url;
        return $this;
    }

    /**
     * Retrieve frame url
     *
     * @return string
     */
    public function getFrameUrl()
    {
        return $this->_frameUrl;
    }
}
