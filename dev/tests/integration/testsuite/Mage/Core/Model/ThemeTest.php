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

class Mage_Core_Model_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test crud operations for theme model using valid data
     *
     * @magentoDbIsolation enabled
     */
    public function testCrud()
    {
        Mage::getConfig();
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $themeModel->setData($this->_getThemeValidData());

        $crud = new Magento_Test_Entity($themeModel, array('theme_version' => '2.0.0.1'));
        $crud->testCrud();
    }

    /**
     * Get theme valid data
     *
     * @return array
     */
    protected function _getThemeValidData()
    {
        return array(
            'area'                 => 'space_area',
            'theme_title'          => 'Space theme',
            'theme_version'        => '2.0.0.0',
            'parent_id'            => null,
            'is_featured'          => false,
            'magento_version_from' => '2.0.0.0-dev1',
            'magento_version_to'   => '*',
            'theme_path'           => 'default/space',
            'preview_image'        => 'images/preview.png',
            'type'                 => Mage_Core_Model_Theme::TYPE_VIRTUAL
        );
    }

    /**
     * Test theme on child relations
     */
    public function testChildRelation()
    {
        /** @var $theme Mage_Core_Model_Theme */
        /** @var $currentTheme Mage_Core_Model_Theme */
        $theme = Mage::getObjectManager()->get('Mage_Core_Model_Theme');
        $collection = $theme->getCollection()->addTypeFilter(Mage_Core_Model_Theme::TYPE_VIRTUAL);
        foreach ($collection as $currentTheme) {
            $parentTheme = $currentTheme->getParentTheme();
            if (!empty($parentTheme)) {
                $this->assertTrue($parentTheme->hasChildThemes());
            }
        }
    }

    /**
     * @magentoDbIsolation enabled
     * @dataProvider getJsCustomizationProvider
     * @param array $filesData
     * @param array $expectedData
     */
    public function testJsCustomization($filesData, $expectedData)
    {
        /** @var $theme Mage_Core_Model_Theme */
        /** @var $themeModel Mage_Core_Model_Theme */
        $theme = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $themeModel = $theme->getCollection()->getFirstItem();

        foreach ($filesData as $fileData) {
            /** @var $filesModel Mage_Core_Model_Theme_File */
            $filesModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme_File');
            $fileData['theme_id'] = $themeModel->getId();
            $filesModel->setData($fileData)
                ->save();
        }

        /** @var $filesJs Mage_Core_Model_Theme_Customization_File_Js */
        $filesJs = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Customization_File_Js');
        $themeFilesCollection = $themeModel->setCustomization($filesJs)
            ->getCustomizationData(Mage_Core_Model_Theme_Customization_File_Js::TYPE);
        $this->assertInstanceOf('Mage_Core_Model_Resource_Theme_File_Collection', $themeFilesCollection);
        $themeFiles = $themeFilesCollection->toArray();
        foreach ($themeFiles['items'] as &$themeFile) {
            $this->assertEquals($themeModel->getId(), $themeFile['theme_id']);
            unset($themeFile['theme_id']);
            unset($themeFile['theme_files_id']);
        }
        $this->assertEquals($expectedData, $themeFiles['items']);
    }

    /**
     * @return array
     */
    public function getJsCustomizationProvider()
    {
        return array(
            array(
                'filesData' => array(
                    array(
                        'file_path'    => 'test_1.js',
                        'file_type'    => Mage_Core_Model_Theme_File::TYPE_JS,
                        'content'      => 'content 1',
                        'sort_order'   => '1'
                    ),
                    array(
                        'file_path'    => 'test_2.js',
                        'file_type'    => Mage_Core_Model_Theme_File::TYPE_JS,
                        'content'      => 'content 2',
                        'sort_order'   => '3'
                    ),
                    array(
                        'file_path'    => 'test_3.js',
                        'file_type'    => Mage_Core_Model_Theme_File::TYPE_JS,
                        'content'      => 'content 3',
                        'sort_order'   => '2'
                    ),
                    array(
                        'file_path'    => 'test_not_js.js',
                        'file_type'    => Mage_Core_Model_Theme_File::TYPE_CSS,
                        'content'      => 'content css',
                        'sort_order'   => ''
                    )
                ),
                'expectedData' => array(
                    array(
                        'file_path'    => 'test_1.js',
                        'file_type'    => Mage_Core_Model_Theme_File::TYPE_JS,
                        'content'      => 'content 1',
                        'sort_order'   => '1',
                        'is_temporary' => '0'
                    ),
                    array(
                        'file_path'    => 'test_3.js',
                        'file_type'    => Mage_Core_Model_Theme_File::TYPE_JS,
                        'content'      => 'content 3',
                        'sort_order'   => '2',
                        'is_temporary' => '0'
                    ),
                    array(
                        'file_path'    => 'test_2.js',
                        'file_type'    => Mage_Core_Model_Theme_File::TYPE_JS,
                        'content'      => 'content 2',
                        'sort_order'   => '3',
                        'is_temporary' => '0'
        ))));
    }
}
