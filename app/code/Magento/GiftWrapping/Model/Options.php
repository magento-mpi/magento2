<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Gift wrapping options model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Model;

class Options extends \Magento\Framework\Object
{
    /**
     * Current data object
     */
    protected $_dataObject = null;

    /**
     * Set gift wrapping options data object
     *
     * @param \Magento\Framework\Object $item
     * @return \Magento\GiftWrapping\Model\Options
     */
    public function setDataObject($item)
    {
        if ($item instanceof \Magento\Framework\Object && $item->getGiftwrappingOptions()) {
            $this->addData(unserialize($item->getGiftwrappingOptions()));
            $this->_dataObject = $item;
        }
        return $this;
    }

    /**
     * Update gift wrapping options data object
     *
     * @return \Magento\GiftWrapping\Model\Options
     */
    public function update()
    {
        $this->_dataObject->setGiftwrappingOptions(serialize($this->getData()));
        return $this;
    }
}
