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
 * @package    Mage_Adminhmtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml order create gift message block
 *
 * @category   Mage
 * @package    Mage_Adminhmtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Giftmessage extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/create/giftmessage.phtml');
    }

    /**
     * Generate form for editing of gift message for entity
     *
     * @param Varien_Object $entity
     * @param string        $entityType
     * @return string
     */
    public function getFormHtml(Varien_Object $entity, $entityType='main') {
        return $this->getLayout()->createBlock(
                    'adminhtml/sales_order_create_giftmessage_form'
               )->setEntity($entity)->setEntityType($entityType)->toHtml();
    }

    /**
     * Retrive items allowed for gift messages.
     *
     * If no items aviable return false.
     *
     * @return array|boolean
     */
    public function getItems()
    {
        $items = array();
        $allItems = $this->getQuote()->getAllItems();
        foreach ($allItems as $item) {
            if($this->helper('giftmessage/message')->getIsMessagesAviable('item', $item, $this->getStore())) {
                $items[] = $item;
            }
        }

        if(sizeof($items)) {
            return $items;
        }

        return false;
    }

} // Class Mage_Adminhmtml_Block_Sales_Order_Create_Giftmessage End