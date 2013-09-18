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
     * Get block model
     *
     * @param PHPUnit_Framework_MockObject_MockObject|Magento_Filesystem $contextFileSystem
     * @param string|null $fileName
     * @return Magento_Install_Block_Begin
     */
    public function getBlockModel($contextFileSystem, $fileName = null)
    {
        $helper = $this->getMock('Magento_Core_Helper_Data', array(), array(), '', false);
        $context = $this->getMock('Magento_Core_Block_Template_Context', array(), array(), '', false);
        $context->expects($this->once())->method('getFileSystem')->will($this->returnValue($contextFileSystem));
        $block = new Magento_Install_Block_Begin($helper, $context, array(), $fileName);
        return $block;
    }

    /**
     * @dataProvider getLicenseHtmlWhenFileExistsDataProvider
     *
     * @param $fileName
     * @param $expectedTxt
     */
    public function testGetLicenseHtmlWhenFileExists($fileName, $expectedTxt)
    {
        $fileSystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $fileSystem->expects($this->once())
            ->method('read')
            ->with($this->equalTo(BP . DS . $fileName))
            ->will($this->returnValue($expectedTxt));

        $block = $this->getBlockModel($fileSystem, $fileName);
        $this->assertEquals($expectedTxt, $block->getLicenseHtml());
    }

    /**
     * Test for getLicenseHtml when EULA file name is empty
     *
     * @dataProvider getLicenseHtmlWhenFileIsEmptyDataProvider
     *
     * @param $fileName
     */
    public function testGetLicenseHtmlWhenFileIsEmpty($fileName)
    {
        $fileSystem = $this->getMock('Magento_Filesystem', array(), array(), '', false);
        $fileSystem->expects($this->never())->method('read');

        $block = $this->getBlockModel($fileSystem, $fileName);
        $this->assertEquals('', $block->getLicenseHtml());
    }

    /**
     * Data provider for testGetLicenseHtmlWhenFileExists
     *
     * @return array
     */
    public function getLicenseHtmlWhenFileExistsDataProvider()
    {
        return array(
            'Lycense for EE' => array(
                'LICENSE_TEST1.html',
                'HTML for EE LICENSE'
            ),
            'Lycense for CE' => array(
                'LICENSE_TEST2.html',
                'HTML for CE LICENSE'
            ),
            'empty file' => array(
                'LICENSE_TEST3.html',
                ''
            )
        );
    }

    /**
     * Data provider for testGetLicenseHtmlWhenFileIsEmpty
     *
     * @return array
     */
    public function getLicenseHtmlWhenFileIsEmptyDataProvider()
    {
        return array(
            'no filename' => array(null),
            'empty filename' => array('')
        );
    }
}
