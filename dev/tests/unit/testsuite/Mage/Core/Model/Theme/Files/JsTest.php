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
 * Test theme service model
 */
class Mage_Core_Model_Theme_Files_JsTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareFileName()
    {
        $fileName = 'js_file.js';

        /** @var $jsFile Mage_Core_Model_Theme_Files_Js */
        $jsFile = $this->getMock(
            'Mage_Core_Model_Theme_Files_Js', array('_getThemeFileByName', 'getId'), array(), '', false
        );

        $jsFile->expects($this->any())
            ->method('_getThemeFileByName')
            ->will($this->returnValue($jsFile));

        $jsFile->expects($this->at(1))
            ->method('getId')
            ->will($this->returnValue(true));

        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = $this->getMock('Mage_Core_Model_Theme', array(), array(), '', false);

        $prepareFileName = new ReflectionMethod($jsFile, '_prepareFileName');
        $prepareFileName->setAccessible(true);
        $result = $prepareFileName->invoke($jsFile, $themeModel, $fileName);
        $this->assertEquals('js_file_1.js', $result);
    }
}
