<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for js files
 */
class Mage_Core_Model_Theme_Customization_File_JsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     * @dataProvider fileSampleData
     */
    public function testSaveJsFile($data)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme = $themeModel->getCollection()->getFirstItem();

        /** @var $jsFileModel Mage_Core_Model_Theme_Customization_File_Js */
        $jsFileModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Customization_File_Js');
        $file = $jsFileModel->saveJsFile($theme, $data);

        $this->assertNotEmpty($file->getId());
    }

    /**
     * @magentoDbIsolation enabled
     * @dataProvider fileSampleData
     */
    public function testGetCollectionByTheme($data)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme = $themeModel->getCollection()->getFirstItem();

        /** @var $jsFileModel Mage_Core_Model_Theme_Customization_File_Js */
        $jsFileModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Customization_File_Js');
        $oldJsFilesCount = $jsFileModel->getCollectionByTheme($theme)->count();
        $oldJsFilesCount++;
        $jsFileModel->saveJsFile($theme, $data);

        $this->assertEquals($oldJsFilesCount, $jsFileModel->getCollectionByTheme($theme)->count());
    }

    /**
     * @magentoDbIsolation enabled
     * @dataProvider fileSampleData
     */
    public function testSaveData($data)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme = $themeModel->getCollection()->getFirstItem();

        /** @var $jsFileModel Mage_Core_Model_Theme_Customization_File_Js */
        $jsFileModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Customization_File_Js');
        /** @var $file  */
        $file = $jsFileModel->saveJsFile($theme, $data);

        $jsFileModel->setDataForSave($file->getId());
        $jsFileModel->saveData($theme);

        /** @var $updatedFile Mage_Core_Model_Theme_File */
        $updatedFile = Mage::getObjectManager()->create('Mage_Core_Model_Theme_File');
        $updatedFile->load($file->getId());

        $this->assertFalse((bool)$updatedFile->getIsTemporary());
    }

    /**
     * @magentoDbIsolation enabled
     * @dataProvider fileSampleData
     */
    public function testRemoveTemporaryFiles($data)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme = $themeModel->getCollection()->getFirstItem();

        /** @var $jsFileModel Mage_Core_Model_Theme_Customization_File_Js */
        $jsFileModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Customization_File_Js');
        $jsFileModel->saveJsFile($theme, $data);

        $oldJsFilesCount = $jsFileModel->getCollectionByTheme($theme)->count();
        $jsFiles = $jsFileModel->getCollectionByTheme($theme);

        $temporaryFilesCount = 0;
        foreach ($jsFiles as $file) {
            if ($file->getIsTemporary()) {
                $temporaryFilesCount++;
            }
        }

        $jsFileModel->removeTemporaryFiles($theme);

        $expectedFilesCount = $oldJsFilesCount - $temporaryFilesCount;
        $this->assertEquals($expectedFilesCount, $jsFileModel->getCollectionByTheme($theme)->count());
    }

    /**
     * File sample data
     *
     * @return array
     */
    public function fileSampleData()
    {
        return array(array(array(
            'name'    => 'js_test_file.js',
            'content' => 'js file content',
        )));
    }
}
