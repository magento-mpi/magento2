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
namespace Magento\GiftRegistry\Block\Customer;

class Share
    extends \Magento\Customer\Block\Account\Dashboard
{
    protected $_formData = null;

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
        return (int)\Mage::helper('Magento\GiftRegistry\Helper\Data')->getRecipientsLimit();
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
            $this->_formData = \Mage::getSingleton('Magento\Customer\Model\Session')
                ->getData('sharing_form', true);
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
