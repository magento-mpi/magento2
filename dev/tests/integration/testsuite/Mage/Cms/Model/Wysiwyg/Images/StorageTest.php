<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Cms
 */
class Mage_Cms_Model_Wysiwyg_Images_StorageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testGetFilesCollection()
    {
        Mage::getDesign()->setDesignTheme('default/default/default', 'adminhtml');
        $model = new Mage_Cms_Model_Wysiwyg_Images_Storage;
        $baseDir = Mage::helper('cms/wysiwyg_images')->getCurrentPath() . __CLASS__;
        mkdir($baseDir, 0777);
        touch($baseDir . DIRECTORY_SEPARATOR . '1.swf');
        $collection = $model->getFilesCollection($baseDir, 'media');
        $this->assertInstanceOf('Mage_Cms_Model_Wysiwyg_Images_Storage_Collection', $collection);
        foreach ($collection as $item) {
            $this->assertInstanceOf('Varien_Object', $item);
            $this->assertStringEndsWith('/1.swf', $item->getUrl());
            $this->assertStringMatchesFormat(
                'http://%s/media/skin/adminhtml/%s/%s/%s/%s/Mage_Cms/images/placeholder_thumbnail.jpg',
                $item->getThumbUrl()
            );
            return;
        }
    }
}
