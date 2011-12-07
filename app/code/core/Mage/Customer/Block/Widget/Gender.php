<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block to render customer's gender attribute
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Block_Widget_Gender extends Mage_Customer_Block_Widget_Abstract
{
    /**
     * Initialize block
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/gender.phtml');
    }

    /**
     * Check if gender attribute enabled in system
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_getAttribute('gender')->getIsVisible();
    }

    /**
     * Check if gender attribute marked as required
     *
     * @return bool
     */
    public function isRequired()
    {
        return (bool)$this->_getAttribute('gender')->getIsRequired();
    }

    /**
     * Get current customer from session
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
    }

    /**
     * Returns options from gender source model
     *
     * @return array
     */
    public function getGenderOptions()
    {
        return Mage::getResourceSingleton('Mage_Customer_Model_Resource_Customer')
            ->getAttribute('gender')
            ->getSource()
            ->getAllOptions();
    }
}
