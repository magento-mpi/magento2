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

class Mage_Core_Model_Design_FileResolution_Strategy_View_ByParamsOnlyTest extends PHPUnit_Framework_TestCase
{
    public function testGetViewFile()
    {
        $dirs = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false);
        $dirs->expects($this->once())
            ->method('getDir')
            ->with(Mage_Core_Model_Dir::STATIC_VIEW)
            ->will($this->returnValue('/themes/dir'));

        $themeModel = $this->getMock(
            'Mage_Core_Model_Theme',
            array('getThemePath'),
            array(),
            '',
            false,
            false
        );
        $themeModel->expects($this->once())
            ->method('getThemePath')
            ->will($this->returnValue('default/test'));

        $model = new Mage_Core_Model_Design_FileResolution_Strategy_View_ByParamsOnly($dirs);

        $filePath = $model->getViewFile('a', $themeModel, '', 'test.txt', 'Some_Module');
        $this->assertEquals('/themes/dir' . DIRECTORY_SEPARATOR . 'a' . DIRECTORY_SEPARATOR . 'default/test'
            . DIRECTORY_SEPARATOR . 'Some_Module' . DIRECTORY_SEPARATOR . 'test.txt', $filePath);
    }
}
