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
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Gift Message url helper
 *
 * @category   Mage
 * @package    Mage_GiftMessage
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_GiftMessage_Helper_Url extends Mage_Core_Helper_Url
{
    public function getEditUrl(Varien_Object $item, $type)
    {
         if($item->getGiftMessageId()) {
             return $this->_getUrl('giftmessage/index/edit', array('message'=>$item->getGiftMessageId(), 'item'=>$item->getId(), 'type'=>$type));
         } else {
             return $this->_getUrl('giftmessage/index/new', array('item'=>$item->getId(), 'type'=>$type));
         }
    }
} // Class Mage_GiftMessage_Helper_Url End