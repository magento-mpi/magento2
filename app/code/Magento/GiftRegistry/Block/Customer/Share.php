<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer gift registry share block
 */
class Magento_GiftRegistry_Block_Customer_Share
    extends Magento_Customer_Block_Account_Dashboard
{
    protected $_formData = null;

    /**
     * Gift registry data
     *
     * @var Magento_GiftRegistry_Helper_Data
     */
    protected $_giftRegistryData = null;

    /**
     * @param Magento_GiftRegistry_Helper_Data $giftRegistryData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Customer_Model_Session $customerSession
     * @param Magento_Newsletter_Model_SubscriberFactory $subscriberFactory
     * @param array $data
     */
    public function __construct(
        Magento_GiftRegistry_Helper_Data $giftRegistryData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Newsletter_Model_SubscriberFactory $subscriberFactory,
        array $data = array()
    ) {
        $this->_giftRegistryData = $giftRegistryData;

        parent::__construct($coreData, $context, $customerSession, $subscriberFactory, $data);
    }

    /**
     * Retrieve form header
     *
     * @return string
     */
    public function getFormHeader()
    {
        $formHeader  = $this->escapeHtml($this->getEntity()->getTitle());
        return __("Share '%1' Gift Registry", $formHeader);
    }

    /**
     * Retrieve escaped customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        return $this->escapeHtml($this->getCustomer()->getName());
    }

    /**
     * Retrieve escaped customer email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->escapeHtml($this->getCustomer()->getEmail());
    }

    /**
     * Retrieve recipients config limit
     *
     * @return int
     */
    public function getRecipientsLimit()
    {
        return (int)$this->_giftRegistryData->getRecipientsLimit();
    }

    /**
     * Retrieve entered data by key
     *
     * @param string $key
     * @return mixed
     */
    public function getFormData($key)
    {
        if (is_null($this->_formData)) {
            $this->_formData = $this->_customerSession->getData('sharing_form', true);
        }
        if (!$this->_formData || !isset($this->_formData[$key])) {
            return null;
        }
        else {
            return $this->escapeHtml($this->_formData[$key]);
        }
    }

    /**
     * Return back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('giftregistry');
    }

    /**
     * Return form send url
     *
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('giftregistry/index/send', array('id' => $this->getEntity()->getId()));
    }
}
