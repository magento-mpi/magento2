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
        $designPath = dirname(__FILE__) . '/_files/design';
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
     * Test get preview image
     */
    public function testGetPreviewImageUrl()
    {
        $themeModel = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $themeModel->setPreviewImage('preview_image.jpg');
        $this->assertEquals('http://localhost/pub/media/theme/preview/preview_image.jpg',
                            $themeModel->getPreviewImageUrl());
    }

    /**
     * Test get preview image default
     */
    public function testGetPreviewImageDefaultUrl()
    {
        $defPreviewImageUrl = 'default_image_preview_url';
        $themeModel = $this->getMock('Mage_Core_Model_Theme', array('_getPreviewImageDefaultUrl'), array(), '', false);
        $themeModel->expects($this->once())
            ->method('_getPreviewImageDefaultUrl')
            ->will($this->returnValue($defPreviewImageUrl));

        $this->assertEquals($defPreviewImageUrl, $themeModel->getPreviewImageUrl());
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
}
