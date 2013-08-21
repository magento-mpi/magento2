<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Block_Widget_Taxvat extends Magento_Customer_Block_Widget_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/taxvat.phtml');
    }

    public function isEnabled()
    {
        return (bool)$this->_getAttribute('taxvat')->getIsVisible();
    }

    public function isRequired()
    {
        return (bool)$this->_getAttribute('taxvat')->getIsRequired();
    }

    public function getCustomer()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
    }
}
