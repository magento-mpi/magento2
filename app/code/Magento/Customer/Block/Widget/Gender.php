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
class Magento_Customer_Block_Widget_Gender extends Magento_Customer_Block_Widget_Abstract
{
    /**
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession;

    /**
     * @var Magento_Customer_Model_Resource_Customer
     */
    protected $_customerResource;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Customer_Model_Resource_Customer $customerResource
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Customer_Model_Session $customerSession,
        Magento_Customer_Model_Resource_Customer $customerResource,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerResource = $customerResource;
        parent::__construct($coreData, $context, $eavConfig, $data);
    }

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
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * Returns options from gender source model
     *
     * @return array
     */
    public function getGenderOptions()
    {
        return $this->_customerResource
            ->getAttribute('gender')
            ->getSource()
            ->getAllOptions();
    }
}
