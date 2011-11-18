<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog image helper
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Helper_Catalog_Category_Image extends Mage_Catalog_Helper_Image
{
    /**
     * Init
     *
     * @param Mage_Catalog_Model_Product $product
     * @param string $attributeName
     * @param string $imageFile
     * @return Mage_XmlConnect_Helper_Catalog_Category_Image
     *
     */
    public function init(Mage_Catalog_Model_Product $product, $attributeName, $imageFile = null)
    {
        return $this;
    }

    /**
     * Init image helper object
     *
     * @param Mage_Catalog_Model_Abstract $category
     * @param string $attributeName
     * @param string $imageFile
     * @return Mage_XmlConnect_Helper_Catalog_Category_Image
     */
    public function initialize(Mage_Catalog_Model_Abstract $category, $attributeName, $imageFile = null)
    {
        $this->_reset();
        $this->_setModel(Mage::getModel('Mage_XmlConnect_Model_Catalog_Category_Image'));
        $this->_getModel()->setDestinationSubdir($attributeName);
        $this->setProduct($category);

        $this->setWatermark(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_image")
        );
        $this->setWatermarkImageOpacity(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_imageOpacity")
        );
        $this->setWatermarkPosition(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_position")
        );
        $this->setWatermarkSize(
            Mage::getStoreConfig("design/watermark/{$this->_getModel()->getDestinationSubdir()}_size")
        );

        if ($imageFile) {
            $this->setImageFile($imageFile);
        } else {
            /*
             * add for work original size
             */
            $this->_getModel()->setBaseFile(
                $this->getProduct()->getData($this->_getModel()->getDestinationSubdir())
            );
        }
        return $this;
    }

    /**
     * Return placeholder image file path
     *
     * @return string
     */
    public function getPlaceholder()
    {
        if (!$this->_placeholder) {
            $attr = $this->_getModel()->getDestinationSubdir();
            $this->_placeholder = 'Mage_XmlConnect::images/catalog/category/placeholder/' . $attr . '.jpg';
        }
        return $this->_placeholder;
    }
}
