<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml order create gift message block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Giftmessage extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * Generate form for editing of gift message for entity
     *
     * @param Magento_Object $entity
     * @param string        $entityType
     * @return string
     */
    public function getFormHtml(Magento_Object $entity, $entityType='quote') {
        return $this->getLayout()
            ->createBlock('Magento_Adminhtml_Block_Sales_Order_Create_Giftmessage_Form')
            ->setEntity($entity)
            ->setEntityType($entityType)
            ->toHtml();
    }

    /**
     * Retrive items allowed for gift messages.
     *
     * If no items available return false.
     *
     * @return array|boolean
     */
    public function getItems()
    {
        $items = array();
        $allItems = $this->getQuote()->getAllItems();

        foreach ($allItems as $item) {
            if($this->_getGiftmessageSaveModel()->getIsAllowedQuoteItem($item)
               && $this->helper('Magento_GiftMessage_Helper_Message')->getIsMessagesAvailable('item',
                        $item, $this->getStore())) {
                // if item allowed
                $items[] = $item;
            }
        }

        if(sizeof($items)) {
            return $items;
        }

        return false;
    }

    /**
     * Retrieve gift message save model
     *
     * @return Magento_Adminhtml_Model_Giftmessage_Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return Mage::getSingleton('Magento_Adminhtml_Model_Giftmessage_Save');
    }

}
