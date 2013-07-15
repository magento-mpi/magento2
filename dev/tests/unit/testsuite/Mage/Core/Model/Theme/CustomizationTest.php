<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test of theme customization model
 */
class Mage_Core_Model_Theme_CustomizationTest extends PHPUnit_Framework_TestCase
{
//
//    /**
//     * @dataProvider getThemeFilesPathDataProvider
//     * @param string $type
//     * @param string $expectedPath
//     */
//    public function testGetThemeFilesPath($type, $expectedPath)
//    {
//        $this->_model->setId(123);
//        $this->_model->setType($type);
//        $this->_model->setArea('area51');
//        $this->_model->setThemePath('theme_path');
//        $this->assertEquals($expectedPath, $this->_model->getThemeFilesPath());
//    }
//
//    /**
//     * @return array
//     */
//    public function getThemeFilesPathDataProvider()
//    {
//        return array(
//            array(Mage_Core_Model_Theme::TYPE_PHYSICAL, 'design/area51/theme_path'),
//            array(Mage_Core_Model_Theme::TYPE_VIRTUAL, 'media/theme_customization/123'),
//            array(Mage_Core_Model_Theme::TYPE_STAGING, 'media/theme_customization/123'),
//        );
//    }

    /**
     * @param $customizationPath
     * @param $themeId
     * @param $expected
     * @dataProvider getCustomViewConfigDataProvider
     */
    public function testGetCustomViewConfigPath($customizationPath, $themeId, PHPUnit_Framework_Constraint $expected)
    {
//        $this->_model->setData('customization_path', $customizationPath);
//        $this->_model->setId($themeId);
//        $actual = $this->_model->getCustomization()->getCustomViewConfigPath();
//        $this->assertThat($actual, $expected);
    }

    /**
     * @return array
     */
    public function getCustomViewConfigDataProvider()
    {
        return array(
            'no custom path, theme is not loaded' => array(
                null, null, $this->isEmpty()
            ),
            'no custom path, theme is loaded' => array(
                null, 'theme_id', $this->equalTo('media/theme_customization/theme_id/view.xml')
            ),
            'with custom path, theme is not loaded' => array(
                'custom/path', null, $this->equalTo('custom/path/view.xml')
            ),
            'with custom path, theme is loaded' => array(
                'custom/path', 'theme_id', $this->equalTo('custom/path/view.xml')
            ),
        );
    }
}
