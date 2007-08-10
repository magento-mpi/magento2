<?php

class Mage_Sales_Model_Invoice_Address extends Mage_Core_Model_Abstract
{

    protected $_invoice;

    protected function _construct()
    {
        $this->_init('sales/invoice_address');
    }

    public function setInvoice(Mage_Sales_Model_Invoice $invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    public function getInvoice()
    {
        return $this->_invoice;
    }

    public function importOrderAddress(Mage_Sales_Model_Order_Address $address)
    {
        $this->setOrderAddressId($address->getId())
            ->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId())
            ->setEmail($address->getEmail())
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCompany($address->getCompany())
            ->setStreet($address->getStreet())
            ->setCity($address->getCity())
            ->setRegion($address->getRegion())
            ->setRegionId($address->getRegionId())
            ->setPostcode($address->getPostcode())
            ->setCountryId($address->getCountryId())
            ->setTelephone($address->getTelephone())
            ->setFax($address->getFax())
        ;
        return $this;
    }

    public function importInvoiceAddress(Mage_Sales_Model_Invoice_Address $address)
    {
        $this->setOrderAddressId($address->getOrderAddressId())
            ->setAddressType($address->getAddressType())
            ->setCustomerId($address->getCustomerId())
            ->setCustomerAddressId($address->getCustomerAddressId())
            ->setEmail($address->getEmail())
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCompany($address->getCompany())
            ->setStreet($address->getStreet())
            ->setCity($address->getCity())
            ->setRegion($address->getRegion())
            ->setRegionId($address->getRegionId())
            ->setPostcode($address->getPostcode())
            ->setCountryId($address->getCountryId())
            ->setTelephone($address->getTelephone())
            ->setFax($address->getFax())
        ;
        return $this;
    }

}
