<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift wrapping options model
 *
 * @category    Enterprise
 * @package     Enterprise_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Model_Options extends Varien_Object
{
    /**
     * Current data object
     */
    protected $_dataObject = null;

    /**
     * Set gift wrapping options data object
     *
     * @param Varien_Object $item
     * @return Enterprise_GiftWrapping_Model_Options
     */
    public function setDataObject($item)
    {
        if ($item instanceof Varien_Object && $item->getGiftwrappingOptions()) {
            $this->addData(unserialize($item->getGiftwrappingOptions()));
            $this->_dataObject = $item;
        }
        return $this;
    }

   /**
     * Update gift wrapping options data object
     *
     * @return Enterprise_GiftWrapping_Model_Options
     */
    public function update()
    {
        $this->_dataObject->setGiftwrappingOptions(serialize($this->getData()));
        return $this;
    }
}
