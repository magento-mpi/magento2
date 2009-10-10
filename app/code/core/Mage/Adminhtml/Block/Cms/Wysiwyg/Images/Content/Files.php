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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory contents block for Wysiwyg Images
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Cms_Wysiwyg_Images_Content_Files extends Mage_Adminhtml_Block_Template
{
    /**
     * Prepare Files collection
     *
     * @return Varien_Data_Collection_Filesystem
     */
    public function getContentsCollection()
    {
        $helper = Mage::helper('cms/wysiwyg_images');
        $type = $this->getRequest()->getParam('type');
        $collection = $helper->getStorage()->getFilesCollection($helper->getCurrentPath(), $type);
        foreach ($collection as $item) {
            $item->setId($helper->idEncode($item->getBasename()));
            $item->setName($helper->getShortFilename($item->getBasename()));
            $item->setUrl($helper->getCurrentUrl() . $item->getBasename());
            $item->setEncodedPath($helper->idEncode($item->getFilename()));

            if(is_file($helper->getCurrentPath() . DS . '.thumbs' . DS . $item->getBasename())) {
                $item->setThumbUrl($helper->getCurrentUrl() . '.thumbs/' . $item->getBasename());
            }

            $size = @getimagesize($item->getFilename());
            if (is_array($size)) {
                $item->setWidth($size[0]);
                $item->setHeight($size[1]);
            }
        }
        return $collection;
    }

    public function getImagesWidth()
    {
        return Mage::getSingleton('cms/wysiwyg_images_storage')->getConfigData('browser_resize_width');
    }

    public function getImagesHeight()
    {
        return Mage::getSingleton('cms/wysiwyg_images_storage')->getConfigData('browser_resize_height');
    }

}
