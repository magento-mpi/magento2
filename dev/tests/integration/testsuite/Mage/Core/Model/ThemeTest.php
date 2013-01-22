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
     */
    public function testCrud()
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $themeModel->setData($this->_getThemeValidData());

        $crud = new Magento_Test_Entity($themeModel, array('theme_version' => '2.0.0.1'));
        $crud->testCrud();
    }

    /**
     * Load from configuration
     *
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testLoadFromConfiguration()
    {
        $designPath = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'design';
        /** @var $themeUtility Mage_Core_Utility_Theme */
        $themeUtility = Mage::getModel('Mage_Core_Utility_Theme', array($designPath));
        $themeUtility->registerThemes()->setDesignTheme('default/default', 'frontend');

        $themePath = implode(DS, array('frontend', 'default', 'default', 'theme.xml'));

        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme = $themeModel->getCollectionFromFilesystem()->setBaseDir($designPath)->addTargetPattern($themePath)
            ->getFirstItem();

        $this->assertEquals($this->_expectedThemeDataFromConfiguration(), $theme->getData());
    }

    /**
     * Expected theme data from configuration
     *
     * @return array
     */
    public function _expectedThemeDataFromConfiguration()
    {
        return array(
            'area'                 => 'frontend',
            'theme_title'          => 'Default',
            'theme_version'        => '2.0.0.0',
            'parent_id'            => null,
            'parent_theme_path'    => null,
            'is_featured'          => true,
            'magento_version_from' => '2.0.0.0-dev1',
            'magento_version_to'   => '*',
            'theme_path'           => 'default/default',
            'preview_image'        => null,
            'theme_directory'      => implode(
                DIRECTORY_SEPARATOR, array(__DIR__, '_files', 'design', 'frontend', 'default', 'default')
            )
        );
    }

    /**
     * Get theme valid data
     *
     * @return array
     */
    protected function _getThemeValidData()
    {
        return array(
            'theme_code'           => 'space',
            'area'                 => 'space_area',
            'theme_title'          => 'Space theme',
            'theme_version'        => '2.0.0.0',
            'parent_id'            => null,
            'parent_theme_path'    => null,
            'is_featured'          => false,
            'magento_version_from' => '2.0.0.0-dev1',
            'magento_version_to'   => '*',
            'theme_path'           => 'default/space',
            'preview_image'        => 'images/preview.png',
        );
    }

    /**
     * Test is virtual
     *
     * @magentoAppIsolation enabled
     */
    public function testIsVirtual()
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $themeModel->setData($this->_getThemeValidData());

        $this->assertTrue($themeModel->isVirtual());
    }

    /**
     * Test id deletable
     *
     * @dataProvider isDeletableDataProvider
     * @param bool $isVirtual
     */
    public function testIsDeletable($isVirtual)
    {
        $themeModel = $this->getMock('Mage_Core_Model_Theme', array('isVirtual'), array(), '', false);
        $themeModel->expects($this->once())
            ->method('isVirtual')
            ->will($this->returnValue($isVirtual));
        $this->assertEquals($isVirtual, $themeModel->isDeletable());
    }

    /**
     * @return array
     */
    public function isDeletableDataProvider()
    {
        return array(array(true), array(false));
    }

    public function testIsThemeCompatible()
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getModel('Mage_Core_Model_Theme');

        $themeModel->setMagentoVersionFrom('2.0.0.0')->setMagentoVersionTo('*');
        $this->assertFalse($themeModel->isThemeCompatible());

        $themeModel->setMagentoVersionFrom('1.0.0.0')->setMagentoVersionTo('*');
        $this->assertTrue($themeModel->isThemeCompatible());
    }

    public function testCheckThemeCompatible()
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getModel('Mage_Core_Model_Theme');

        $themeModel->setMagentoVersionFrom('2.0.0.0')->setMagentoVersionTo('*')->setThemeTitle('Title');
        $themeModel->checkThemeCompatible();
        $this->assertEquals('Title (incompatible version)', $themeModel->getThemeTitle());

        $themeModel->setMagentoVersionFrom('1.0.0.0')->setMagentoVersionTo('*')->setThemeTitle('Title');
        $themeModel->checkThemeCompatible();
        $this->assertEquals('Title', $themeModel->getThemeTitle());
    }

    public function testGetLabelsCollection()
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getModel('Mage_Core_Model_Theme');

        /** @var $expectedCollection Mage_Core_Model_Theme_Collection */
        $expectedCollection = Mage::getModel('Mage_Core_Model_Resource_Theme_Collection');
        $expectedCollection->addFilter('area', 'frontend');

        $expectedItemsCount = count($expectedCollection);

        $labelsCollection = $themeModel->getLabelsCollection();
        $this->assertEquals($expectedItemsCount, count($labelsCollection));

        $labelsCollection = $themeModel->getLabelsCollection('-- Please Select --');
        $this->assertEquals(++$expectedItemsCount, count($labelsCollection));
    }

    /**
     * Test theme on child relations
     */
    public function testChildRelation()
    {
        /** @var $theme Mage_Core_Model_Theme */
        /** @var $currentTheme Mage_Core_Model_Theme */
        $theme = Mage::getObjectManager()->get('Mage_Core_Model_Theme');
        foreach ($theme->getCollection() as $currentTheme) {
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
            /** @var $filesModel Mage_Core_Model_Theme_Files */
            $filesModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Files');
            $fileData['theme_id'] = $themeModel->getId();
            $filesModel->setData($fileData)
                ->save();
        }

        /** @var $filesJs Mage_Core_Model_Theme_Customization_Files_Js */
        $filesJs = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Customization_Files_Js');
        $themeFilesCollection = $themeModel->setCustomization($filesJs)
            ->getCustomizationData(Mage_Core_Model_Theme_Customization_Files_Js::TYPE);
        $this->assertInstanceOf('Mage_Core_Model_Resource_Theme_Files_Collection', $themeFilesCollection);
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
                        'file_name'    => 'test_1.js',
                        'file_type'    => Mage_Core_Model_Theme_Files::TYPE_JS,
                        'content'      => 'content 1',
                        'order'        => '1'
                    ),
                    array(
                        'file_name'    => 'test_2.js',
                        'file_type'    => Mage_Core_Model_Theme_Files::TYPE_JS,
                        'content'      => 'content 2',
                        'order'        => '3'
                    ),
                    array(
                        'file_name'    => 'test_3.js',
                        'file_type'    => Mage_Core_Model_Theme_Files::TYPE_JS,
                        'content'      => 'content 3',
                        'order'        => '2'
                    ),
                    array(
                        'file_name'    => 'test_not_js.js',
                        'file_type'    => Mage_Core_Model_Theme_Files::TYPE_CSS,
                        'content'      => 'content css',
                        'order'        => ''
                    )
                ),
                'expectedData' => array(
                    array(
                        'file_name'    => 'test_1.js',
                        'file_type'    => Mage_Core_Model_Theme_Files::TYPE_JS,
                        'content'      => 'content 1',
                        'order'        => '1',
                        'is_temporary' => '0'
                    ),
                    array(
                        'file_name'    => 'test_3.js',
                        'file_type'    => Mage_Core_Model_Theme_Files::TYPE_JS,
                        'content'      => 'content 3',
                        'order'        => '2',
                        'is_temporary' => '0'
                    ),
                    array(
                        'file_name'    => 'test_2.js',
                        'file_type'    => Mage_Core_Model_Theme_Files::TYPE_JS,
                        'content'      => 'content 2',
                        'order'        => '3',
                        'is_temporary' => '0'
        ))));
    }
}
