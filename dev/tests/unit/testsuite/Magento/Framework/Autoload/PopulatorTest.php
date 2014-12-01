<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Framework\Autoload;

use Magento\Framework\App\Filesystem\DirectoryList;

class PopulatorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\App\Filesystem\DirectoryList | \PHPUnit_Framework_MockObject_MockObject */
    protected $mockDirectoryList;

    public function setUp()
    {
        $this->mockDirectoryList = $this->getMockBuilder('\Magento\Framework\App\Filesystem\DirectoryList')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockDirectoryList->expects($this->any())
            ->method('getPath')
            ->willReturnArgument(0);
    }

    public function testPopulateMappings()
    {
        $mockAutoloader = $this->getMockBuilder('\Magento\Framework\Autoload\AutoloaderInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $mockAutoloader->expects($this->at(0))
            ->method('addPsr4')
            ->with('Magento\\', [DirectoryList::MODULES . '/Magento/', DirectoryList::GENERATION . '/Magento/'], true);
        $mockAutoloader->expects($this->at(1))
            ->method('addPsr4')
            ->with('Zend\\Soap\\', DirectoryList::MODULES . '/Zend/Soap/', true);
        $mockAutoloader->expects($this->at(2))
            ->method('addPsr4')
            ->with('Zend\\', DirectoryList::LIB_INTERNAL . '/Zend/', true);

        $mockAutoloader->expects($this->at(3))
            ->method('addPsr0')
            ->with('Apache_', DirectoryList::LIB_INTERNAL, true);
        $mockAutoloader->expects($this->at(4))
            ->method('addPsr0')
            ->with('Cm_', DirectoryList::LIB_INTERNAL, true);
        $mockAutoloader->expects($this->at(5))
            ->method('addPsr0')
            ->with('Credis_', DirectoryList::LIB_INTERNAL, true);
        $mockAutoloader->expects($this->at(6))
            ->method('addPsr0')
            ->with('Less_', DirectoryList::LIB_INTERNAL, true);
        $mockAutoloader->expects($this->at(7))
            ->method('addPsr0')
            ->with('Symfony\\', DirectoryList::LIB_INTERNAL, true);
        $mockAutoloader->expects($this->at(8))
            ->method('addPsr0')
            ->with('Zend_Date', DirectoryList::MODULES, true);
        $mockAutoloader->expects($this->at(9))
            ->method('addPsr0')
            ->with('Zend_Mime', DirectoryList::MODULES, true);
        $mockAutoloader->expects($this->at(10))
            ->method('addPsr0')
            ->with('Zend_', DirectoryList::LIB_INTERNAL, true);
        $mockAutoloader->expects($this->at(11))
            ->method('addPsr0')
            ->with('Zend\\', DirectoryList::LIB_INTERNAL, true);
        $mockAutoloader->expects($this->at(12))
            ->method('addPsr0')
            ->with('', [DirectoryList::MODULES, DirectoryList::GENERATION]);

        Populator::populateMappings($mockAutoloader, $this->mockDirectoryList);
    }
}
