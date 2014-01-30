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
 * Test class for \Magento\Install\Block\Begin
 */
namespace Magento\Install\Block;

class BeginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @dataProvider getLicenseHtmlWhenFileExistsDataProvider
     *
     * @param $fileName
     * @param $expectedTxt
     */
    public function testGetLicenseHtmlWhenFileExists($fileName, $expectedTxt)
    {
        $directoryMock = $this->getMock('Magento\Filesystem\Directory\Read', array(), array(), '', false);
        $directoryMock->expects($this->once())
            ->method('readFile')
            ->with($this->equalTo($fileName))
            ->will($this->returnValue($expectedTxt));

        $fileSystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $fileSystem->expects($this->once())
            ->method('getDirectoryRead')
            ->will($this->returnValue($directoryMock));

        $block = $this->_objectManager->getObject('Magento\Install\Block\Begin',
            array('filesystem' => $fileSystem, 'eulaFile' => $fileName));

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
        $fileSystem = $this->getMock('Magento\App\Filesystem', array(), array(), '', false);
        $fileSystem->expects($this->never())->method('read');

        $block = $this->_objectManager->getObject('Magento\Install\Block\Begin',
            array('filesystem' => $fileSystem, 'eulaFile' => $fileName));
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
