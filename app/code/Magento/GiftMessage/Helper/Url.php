<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift Message url helper
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftMessage_Helper_Url extends Magento_Core_Helper_Url
{
    /**
     * Retrive gift message save url
     *
     * @param Magento_Object $item
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getEditUrl(Magento_Object $item, $type, $params=array())
    {
         if($item->getGiftMessageId()) {
             $params = array_merge($params, array('message'=>$item->getGiftMessageId(), 'item'=>$item->getId(), 'type'=>$type));
             return $this->_getUrl('giftmessage/index/edit', $params);
         } else {
             $params = array_merge($params, array('item'=>$item->getId(), 'type'=>$type));
             return $this->_getUrl('giftmessage/index/new', $params);
         }
    }

    /**
     * Retrive gift message button block url
     *
     * @param integer $itemId
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getButtonUrl($itemId, $type, $params=array())
    {
         $params = array_merge($params, array('item'=>$itemId, 'type'=>$type));
         return $this->_getUrl('giftmessage/index/button', $params);
    }

    /**
     * Retrive gift message remove url
     *
     * @param integer $itemId
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getRemoveUrl($itemId, $type, $params=array())
    {
         $params = array_merge($params, array('item'=>$itemId, 'type'=>$type));
         return $this->_getUrl('giftmessage/index/remove', $params);
    }

    /**
     * Retrive gift message save url
     *
     * @param integer $itemId
     * @param string $type
     * @param string $giftMessageId
     * @param array $params
     * @return string
     */
    public function getSaveUrl($itemId, $type, $giftMessageId=null, $params=array())
    {
         if(!is_null($giftMessageId)) {
             $params = array_merge($params, array('message'=>$giftMessageId, 'item'=>$itemId, 'type'=>$type));
             return $this->_getUrl('giftmessage/index/save', $params);
         } else {
             $params = array_merge($params, array('item'=>$itemId, 'type'=>$type));
             return $this->_getUrl('giftmessage/index/save', $params);
         }
    }

}
