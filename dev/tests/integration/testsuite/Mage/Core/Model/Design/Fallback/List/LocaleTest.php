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

class Mage_Core_Model_Design_Fallback_List_LocaleTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Design_Fallback_List_File
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getObjectManager()->create('Mage_Core_Model_Design_Fallback_List_Locale');
    }

    public function testGetPatternsDirs()
    {
        $dir = Mage::getObjectManager()->get('Mage_Core_Model_Dir');

        $parentTheme = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $parentTheme->setThemePath('parent_theme_path');

        $theme = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme->setThemePath('theme_path');
        $theme->setParentTheme($parentTheme);

        $params = array(
            'theme' => $theme,
            'area' => 'area',
            'locale' => 'locale'
        );

        $actualResult = $this->_model->getPatternDirs($params);

        $expectedResult = array(
            str_replace('/', DIRECTORY_SEPARATOR,
                $dir->getDir(Mage_Core_Model_Dir::THEMES) . '/area/theme_path/locale/locale'),
            str_replace('/', DIRECTORY_SEPARATOR,
                $dir->getDir(Mage_Core_Model_Dir::THEMES) . '/area/parent_theme_path/locale/locale'),
        );

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @dataProvider getPatternsDirsDataExceptionProvider
     * @expectedException InvalidArgumentException
     */
    public function testGetPatternsDirsExceptions($setParams)
    {
        $theme = Mage::getObjectManager()->create('Mage_Core_Model_Theme');
        $theme->setThemePath('theme_path');

        $params = array(
            'theme' => $theme,
            'area' => 'area',
            'locale' => 'locale'
        );
        $params = array_merge($params, $setParams);

        $this->_model->getPatternDirs($params);
    }

    public function getPatternsDirsDataExceptionProvider()
    {
        return array(
            'No theme' => array(array('theme' => null)),
            'No area' => array(array('area' => null)),
            'No locale' => array(array('locale' => null)),
        );
    }
}
