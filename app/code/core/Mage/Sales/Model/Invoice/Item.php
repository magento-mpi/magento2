<?php

class Mage_Sales_Model_Invoice_Item extends Mage_Core_Model_Abstract
{
    protected $_invoice;

    function _construct()
    {
        $this->_init('sales/invoice_item');
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

    public function importOrderItem(Mage_Sales_Model_Order_Item $item)
    {
        $this->setParentId($this->getInvoice()->getId())
            ->setStoreId($item->getStoreId())
            ->setOrderItemId($item->getId())
            ->setProductId($item->getProductId())
            ->setName($item->getName())
            ->setDescription($item->getDescription())
            ->setSku($item->getSku())
            ->setPrice($item->getPrice())
            ->setCost($item->getCost())
        ;
        return $this;
    }

    public function importInvoiceItem(Mage_Sales_Model_Invoice_Item $item)
    {
        $this->setParentId($this->getInvoice()->getId())
            ->setStoreId($item->getStoreId())
            ->setOrderItemId($item->getOrderItemId())
            ->setProductId($item->getProductId())
            ->setName($item->getName())
            ->setDescription($item->getDescription())
            ->setSku($item->getSku())
            ->setPrice($item->getPrice())
            ->setCost($item->getCost())
        ;
        return $this;
    }

    public function calcRowTotal()
    {
        $this->setRowTotal($this->getPrice()*$this->getQty());
        return $this;
    }

    public function calcRowWeight()
    {
        $this->setRowWeight($this->getWeight()*$this->getQty());
        return $this;
    }

    public function calcTaxAmount()
    {
        $this->setTaxAmount($this->getRowTotal() * $this->getTaxPercent()/100);
        return $this;
    }

}