<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Block_Adminhtml_Customer_Edit_Tab_Giftregistry
    extends Magento_Adminhtml_Block_Template
    implements Magento_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Set identifier and title
     */
    protected  function _construct()
    {
        parent::_construct();
        $this->setId('gifregustry');
        $this->setTitle(__('Gift Registry'));
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getTitle();
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTitle();
    }

    /**
     * Check whether tab can be showed
     *
     * @return bool
     */
    public function canShowTab()
    {
        $customer = Mage::registry('current_customer');
        return $customer->getId()
           && Mage::helper('Enterprise_GiftRegistry_Helper_Data')->isEnabled()
           && $this->_authorization->isAllowed('Enterprise_GiftRegistry::customer_enterprise_giftregistry');
    }

    /**
     * Check whether tab should be hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Precessor tab ID getter
     *
     * @return string
     */
    public function getAfter()
    {
        return 'reviews';
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Tab URL getter
     *
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/giftregistry_customer/grid', array('_current' => true));
    }
}
