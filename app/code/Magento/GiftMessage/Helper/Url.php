<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Helper;

/**
 * Gift Message url helper
 *
 * @category   Magento
 * @package    Magento_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Url extends \Magento\Core\Helper\Url
{
    /**
     * Retrieve gift message save url
     *
     * @param \Magento\Object $item
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getEditUrl(\Magento\Object $item, $type, $params = array())
    {
        if ($item->getGiftMessageId()) {
            $params = array_merge(
                $params,
                array('message' => $item->getGiftMessageId(), 'item' => $item->getId(), 'type' => $type)
            );
            return $this->_getUrl('giftmessage/index/edit', $params);
        } else {
            $params = array_merge($params, array('item' => $item->getId(), 'type' => $type));
            return $this->_getUrl('giftmessage/index/new', $params);
        }
    }

    /**
     * Retrieve gift message button block url
     *
     * @param int $itemId
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getButtonUrl($itemId, $type, $params = array())
    {
        $params = array_merge($params, array('item' => $itemId, 'type' => $type));
        return $this->_getUrl('giftmessage/index/button', $params);
    }

    /**
     * Retrieve gift message remove url
     *
     * @param int $itemId
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getRemoveUrl($itemId, $type, $params = array())
    {
        $params = array_merge($params, array('item' => $itemId, 'type' => $type));
        return $this->_getUrl('giftmessage/index/remove', $params);
    }

    /**
     * Retrieve gift message save url
     *
     * @param int $itemId
     * @param string $type
     * @param string $giftMessageId
     * @param array $params
     * @return string
     */
    public function getSaveUrl($itemId, $type, $giftMessageId = null, $params = array())
    {
        if (!is_null($giftMessageId)) {
            $params = array_merge($params, array('message' => $giftMessageId, 'item' => $itemId, 'type' => $type));
            return $this->_getUrl('giftmessage/index/save', $params);
        } else {
            $params = array_merge($params, array('item' => $itemId, 'type' => $type));
            return $this->_getUrl('giftmessage/index/save', $params);
        }
    }
}
