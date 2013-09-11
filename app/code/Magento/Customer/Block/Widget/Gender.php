<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block to render customer's gender attribute
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Widget;

class Gender extends \Magento\Customer\Block\Widget\AbstractWidget
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
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();
    }

    /**
     * Returns options from gender source model
     *
     * @return array
     */
    public function getGenderOptions()
    {
        return \Mage::getResourceSingleton('\Magento\Customer\Model\Resource\Customer')
            ->getAttribute('gender')
            ->getSource()
            ->getAllOptions();
    }
}
