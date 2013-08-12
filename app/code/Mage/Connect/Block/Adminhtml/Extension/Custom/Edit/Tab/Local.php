<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Magento Connect View Local extensions Tab block
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Local
    extends Magento_Backend_Block_Template
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * Retrieve Tab load URL
     *
     * @return  string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/loadtab', array('_current' => true));
    }

    /**
     * Retrieve class for load by ajax
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Retrieve class for load by ajax
     *
     * @return string
     */
    public function getClass()
    {
        return 'ajax';
    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('Mage_Connect_Helper_Data')->__('Load Local Package');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('Mage_Connect_Helper_Data')->__('Load Local Package');
    }

    /**
     * Is can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Is hidden tab
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
