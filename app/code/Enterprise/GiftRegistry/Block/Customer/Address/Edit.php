<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GiftRegistry shipping Address block
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 * @author     Magento Core Team <core@magentocommerce.com>
 */
 class Enterprise_GiftRegistry_Block_Customer_Address_Edit extends Enterprise_GiftRegistry_Block_Customer_Edit_Abstract
{

    /**
     * Contains logged in customer
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    /**
     * Getter for entity object
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function getEntity()
    {
        return Mage::registry('enterprise_giftregistry_entity');
    }

    /**
     * Getter for address object
     *
     * @return Mage_Customer_Model_Address
     */
    public function getAddress()
    {
        return Mage::registry('enterprise_giftregistry_address');
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
                'value' => Enterprise_GiftRegistry_Helper_Data::ADDRESS_NONE,
                'label' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('None')
            ));
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = array(
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                );
            }
            $options[] = array(
                'value' => Enterprise_GiftRegistry_Helper_Data::ADDRESS_NEW,
                'label' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('New Address')
            );

            $select = $this->getLayout()->createBlock('Mage_Core_Block_Html_Select')
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
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer();
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
        return Mage::getSingleton('Mage_Customer_Model_Session')->isLoggedIn();
    }
}