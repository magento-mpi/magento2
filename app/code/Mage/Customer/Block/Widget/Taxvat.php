<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Customer_Block_Widget_Taxvat extends Mage_Customer_Block_Widget_Abstract
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
        return Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
    }
}
