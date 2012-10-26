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
 * Product images gallery block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Product_Gallery extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Generate images gallery xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $productId = $this->getRequest()->getParam('id', null);
        $product = Mage::getModel('Mage_Catalog_Model_Product')->setStoreId(Mage::app()->getStore()->getId())->load($productId);
        $collection = $product->getMediaGalleryImages();

        $imagesNode = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<images></images>'));
        $imageHelper = $this->helper('Mage_Catalog_Helper_Image');

        foreach ($collection as $item) {
            $imageNode = $imagesNode->addChild('image');

            /**
             * Big image
             */
            $bigImage = $imageHelper->init($product, 'image', $item->getFile())
                ->constrainOnly(true)->keepFrame(false)
                ->resize($imageHelper->getImageSizeForContent('product_gallery_big'));

            $fileNode = $imageNode->addChild('file');
            $fileNode->addAttribute('type', 'big');
            $fileNode->addAttribute('url', $bigImage);

            $file = Mage::helper('Mage_XmlConnect_Helper_Data')->urlToPath($bigImage);

            $fileNode->addAttribute('id', ($id = $item->getId()) ? (int) $id : 0);
            $fileNode->addAttribute('modification_time', filemtime($file));

            /**
             * Small image
             */
            $smallImage = $imageHelper->init($product, 'thumbnail', $item->getFile())
                ->constrainOnly(true)->keepFrame(false)
                ->resize($imageHelper->getImageSizeForContent('product_gallery_small'));

            $fileNode = $imageNode->addChild('file');
            $fileNode->addAttribute('type', 'small');
            $fileNode->addAttribute('url', $smallImage);

            $file = Mage::helper('Mage_XmlConnect_Helper_Data')->urlToPath($smallImage);
            $fileNode->addAttribute('modification_time', filemtime($file));
        }
        return $imagesNode->asNiceXml();
    }
}
