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
 * GiftRegistry shipping Address block
 */
class Magento_GiftRegistry_Block_Customer_Address_Edit extends Magento_GiftRegistry_Block_Customer_Edit_Abstract
{
    /**
     * Contains logged in customer
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Getter for entity object
     * @return Magento_GiftRegistry_Model_Entity
     */
    public function getEntity()
    {
        return $this->_coreRegistry->registry('magento_giftregistry_entity');
    }

    /**
     * Getter for address object
     *
     * @return Magento_Customer_Model_Address
     */
    public function getAddress()
    {
        return $this->_coreRegistry->registry('magento_giftregistry_address');
    }

    /**
     * Check customer has address
     *
     * @return bool
     */
    public function customerHasAddresses()
    {
        return count($this->getCustomer()->getAddresses());
    }

    /**
     * Return html select input element for Address (None/<address1>/<address2>/New/)
     *
     * @param string $domId
     * @return html
     */
    public function getAddressHtmlSelect($domId = 'address_type_or_id')
    {
        if ($this->isCustomerLoggedIn()) {
            $options = array(array(
                'value' => Magento_GiftRegistry_Helper_Data::ADDRESS_NONE,
                'label' => __('None')
            ));
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }
            $options[] = array(
                'value' => Magento_GiftRegistry_Helper_Data::ADDRESS_NEW,
                'label' => __('New Address')
            );

            $select = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
                ->setName('address_type_or_id')
                ->setId($domId)
                ->setClass('address-select')
                ->setOptions($options);

            return $select->getHtml();
        }
        return '';
    }

    /**
     * Get logged in customer
     *
     * @return Magento_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer();
        }
        return $this->_customer;
    }

    /**
     * Checking customer loggin status
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->isLoggedIn();
    }
}
