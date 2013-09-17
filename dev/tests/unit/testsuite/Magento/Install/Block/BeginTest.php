<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Install_Block_Begin
 */
class Magento_Install_Block_BeginTest extends PHPUnit_Framework_TestCase
{

    /**
     * @covers Magento_Install_Block_Begin::getLicenseHtml
     * @dataProvider getLicenseHtmlDataProvider
     *
     * @param $fileName
     * @param $expectedTxt
     */
    public function testGetRobotsDefaultCustomInstructions($fileName, $expectedTxt)
    {
        $fileSystem = $this->getMock('Magento_Filesystem', array('read'), array(), '', false);
        if ($fileName) {
            $fileSystem->expects($this->once())
                ->method('read')
                ->with($this->equalTo(BP . DS . $fileName))
                ->will($this->returnValue($expectedTxt));
        } else {
            $fileSystem->expects($this->never())
                ->method('read');
        }

        $helper = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $context = $this->getMock('Magento_Core_Block_Template_Context', array('getFileSystem'), array(), '', false);
        $context->expects($this->once())->method('getFileSystem')->will($this->returnValue($fileSystem));
        $block = new Magento_Install_Block_Begin($helper, $context, array(), $fileName);

        $this->assertEquals($expectedTxt, $block->getLicenseHtml());
    }

    /**
     * Data provider for testGetRobotsDefaultCustomInstructions
     *
     * @return array
     */
    public function getLicenseHtmlDataProvider()
    {
        return array(
            'Lycense for EE' => array(
                'LICENSE_EE.html',
                'HTML for EE LICENSE'
            ),
            'Lycense for CE' => array(
                'LICENSE_CE.html',
                'HTML for CE LICENSE'
            ),
            'no filename' => array(
                null,
                ''
            ),
            'empty filename' => array(
                '',
                ''
            )
        );
    }
}
