<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Sales_Model_Quote_Address_Item extends Mage_Sales_Model_Quote_Item_Abstract
{
    /**
     * Quote address model object
     *
     * @var Mage_Sales_Model_Quote_Address
     */
    protected $_address;
    
    protected function _construct()
    {
        $this->_init('sales/quote_address_item');
    }
    
    /**
     * Declare address model
     *
     * @param   Mage_Sales_Model_Quote_Address $address
     * @return  Mage_Sales_Model_Quote_Address_Item
     */
    public function setAddress(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        return $this;
    }
    
    /**
     * Retrieve address model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    public function getAddress()
    {
        return $this->_address;
    }


    public function importQuoteItem(Mage_Sales_Model_Quote_Item $quoteItem)
    {
        $item = clone $quoteItem;
        $item->setQuoteItemId($item->getId());
        $qty = $item->getQty();
        
        $item->unsEntityId()
            ->unsAttributeSetId()
            ->unsEntityTypeId()
            ->unsQty()
            ->unsParentId();
            
        $this->addData($item->getData());

        if (!$this->hasQty()) {
            $this->setQty($qty);
        }
        
        $this->setQuoteItemImported(true);
        return $this;
    }
}