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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product images gallery block
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Catalog_Product_Gallery extends Mage_XmlConnect_Block_Catalog
{

    /**
     * Product gallery image sizes
     */
    const PRODUCT_GALLERY_BIG_IMAGE_SIZE_PARAM   = 280;
    const PRODUCT_GALLERY_SMALL_IMAGE_SIZE_PARAM = 40;

    /**
     * Generate images gallery xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $productId = $this->getRequest()->getParam('id', null);
        $product = Mage::getModel('catalog/product')
            ->setStoreId(Mage::app()->getStore()->getId())
            ->load($productId);
        $collection = $product->getMediaGalleryImages();

        $imagesNode = new Varien_Simplexml_Element('<images></images>');
        $helper = $this->helper('catalog/image');

        foreach ($collection as $item) {
            $imageNode = $imagesNode->addChild('image');

            /**
             * Big image
             */
            $bigImage = $helper->init($product, 'thumbnail', $item->getFile())
                ->resize(self::PRODUCT_GALLERY_BIG_IMAGE_SIZE_PARAM);

            $fileNode = $imageNode->addChild('file');
            $fileNode->addAttribute('type', 'big');
            $fileNode->addAttribute('url', $bigImage);

            /**
             * Small image
             */
            $smallImage = $helper->init($product, 'thumbnail', $item->getFile())
                ->resize(self::PRODUCT_GALLERY_SMALL_IMAGE_SIZE_PARAM);

            $fileNode = $imageNode->addChild('file');
            $fileNode->addAttribute('type', 'small');
            $fileNode->addAttribute('url', $smallImage);
        }
        return $imagesNode->asNiceXml();
    }
}
