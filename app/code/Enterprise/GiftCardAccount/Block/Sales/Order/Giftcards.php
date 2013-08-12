<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCardAccount_Block_Sales_Order_Giftcards extends Magento_Core_Block_Template
{
    /**
     * Retrieve current order model instance
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * Retreive gift cards applied to current order
     *
     * @return array
     */
    public function getGiftCards()
    {
        $result = array();
        $source = $this->getSource();
        if (!($source instanceof Magento_Sales_Model_Order)) {
            return $result;
        }
        $cards = Mage::helper('Enterprise_GiftCardAccount_Helper_Data')->getCards($this->getOrder());
        foreach ($cards as $card) {
            $obj = new Magento_Object();
            $obj->setBaseAmount($card['ba'])
                ->setAmount($card['a'])
                ->setCode($card['c']);

            $result[] = $obj;
        }
        return $result;
    }

    /**
     * Initialize giftcard order total
     *
     * @return Enterprise_GiftCardAccount_Block_Sales_Order_Giftcards
     */
    public function initTotals()
    {
        $total = new Magento_Object(array(
            'code'      => $this->getNameInLayout(),
            'block_name'=> $this->getNameInLayout(),
            'area'      => $this->getArea()
        ));
        $this->getParentBlock()->addTotalBefore($total, array('customerbalance', 'grand_total'));
        return $this;
    }

    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }
}
