<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tool block with buttons
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_VersionsCms_Block_Adminhtml_Cms_Page_Preview_Buttons extends Magento_Adminhtml_Block_Widget_Container
{
    /**
     * Adding two main buttons
     *
     * @return Magento_VersionsCms_Block_Adminhtml_Cms_Page_Preview_Buttons
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_addButton('preview', array(
                'id' => 'preview-buttons-preview',
                'label' => 'Preview',
                'class' => 'preview',
                'onclick' => 'preview()'
            ));

        if (Mage::getSingleton('Magento_VersionsCms_Model_Config')->canCurrentUserPublishRevision()) {
            $this->_addButton('publish', array(
                'id' => 'preview-buttons-publish',
                'label' => 'Publish',
                'class' => 'publish',
                'onclick' => 'publish()'
            ));
        }
    }

    /**
     * Override parent method to produce only button's html in result
     *
     * @return string
     */
    protected function _toHtml()
    {
        parent::_toHtml();
        return $this->getButtonsHtml();
    }
}
