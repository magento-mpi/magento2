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
 * Customer login form block
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Block_Form_Login extends Magento_Core_Block_Template
{
    private $_username = -1;

    /**
     * Customer data
     *
     * @var Magento_Customer_Helper_Data
     */
    protected $_customerData = null;

    /**
     * @param Magento_Customer_Helper_Data $customerData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Customer_Helper_Data $customerData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_customerData = $customerData;
        parent::__construct($coreData, $context, $data);
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(__('Customer Login'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->_customerData->getLoginPostUrl();
    }

    /**
     * Retrieve create new account url
     *
     * @return string
     */
    public function getCreateAccountUrl()
    {
        $url = $this->getData('create_account_url');
        if (is_null($url)) {
            $url = $this->_customerData->getRegisterUrl();
        }
        return $url;
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->_customerData->getForgotPasswordUrl();
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        if (-1 === $this->_username) {
            $this->_username = Mage::getSingleton('Magento_Customer_Model_Session')->getUsername(true);
        }
        return $this->_username;
    }
}
