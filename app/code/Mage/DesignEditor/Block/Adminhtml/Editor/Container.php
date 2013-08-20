<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Editor toolbar
 *
 * @method Mage_DesignEditor_Block_Adminhtml_Editor_Container setTheme(Mage_Core_Model_Theme $theme)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Container extends Mage_Backend_Block_Widget_Container
{
    /**
     * Frame Url
     *
     * @var string
     */
    protected $_frameUrl;

    /**
     * Add elements in layout
     */
    protected function _prepareLayout()
    {
        $this->addButton('back_button', array(
            'label'   => __('Back'),
            'onclick' => 'setLocation(\'' . $this->getUrl('*/*') . '\')',
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
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Container
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
