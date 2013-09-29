<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping options model
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Model;

class Options extends \Magento\Object
{
    /**
     * Current data object
     */
    protected $_dataObject = null;

    /**
     * Set gift wrapping options data object
     *
     * @param \Magento\Object $item
     * @return \Magento\GiftWrapping\Model\Options
     */
    public function setDataObject($item)
    {
        if ($item instanceof \Magento\Object && $item->getGiftwrappingOptions()) {
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
