<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftMessage\Helper;

/**
 * Gift Message url helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Url extends \Magento\Core\Helper\Url
{
    /**
     * Retrieve gift message save url
     *
     * @param \Magento\Framework\Object $item
     * @param string $type
     * @param array $params
     * @return string
     */
    public function getEditUrl(\Magento\Framework\Object $item, $type, $params = [])
    {
        if ($item->getGiftMessageId()) {
            $params = array_merge(
                $params,
                ['message' => $item->getGiftMessageId(), 'item' => $item->getId(), 'type' => $type]
            );
            return $this->_getUrl('giftmessage/index/edit', $params);
        } else {
            $params = array_merge($params, ['item' => $item->getId(), 'type' => $type]);
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
    public function getButtonUrl($itemId, $type, $params = [])
    {
        $params = array_merge($params, ['item' => $itemId, 'type' => $type]);
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
    public function getRemoveUrl($itemId, $type, $params = [])
    {
        $params = array_merge($params, ['item' => $itemId, 'type' => $type]);
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
    public function getSaveUrl($itemId, $type, $giftMessageId = null, $params = [])
    {
        if (!is_null($giftMessageId)) {
            $params = array_merge($params, ['message' => $giftMessageId, 'item' => $itemId, 'type' => $type]);
            return $this->_getUrl('giftmessage/index/save', $params);
        } else {
            $params = array_merge($params, ['item' => $itemId, 'type' => $type]);
            return $this->_getUrl('giftmessage/index/save', $params);
        }
    }
}
