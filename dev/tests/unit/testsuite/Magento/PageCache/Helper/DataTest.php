<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\PageCache\Helper\Data
 */
namespace Magento\PageCache\Helper;

/**
 * Class DataTest
 *
 * @package Magento\PageCache\Controller
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\PageCache\Helper\Data
     */
    protected $helper;

    /**
     * Set up before test
     */
    protected function setUp()
    {
        $context = $this->getMockBuilder('\Magento\App\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();

        $this->configMock = $this->getMockBuilder('\Magento\Core\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->helper = new \Magento\PageCache\Helper\Data($context, $this->configMock);
    }

    public function testGetMaxAgeCache()
    {
        $age = time();
        $this->configMock->expects($this->once())
            ->method('getValue')
            ->with($this->equalTo(\Magento\PageCache\Helper\Data::MAX_AGE_PATH))
            ->will($this->returnValue($age));
        $data = $this->helper->getMaxAgeCache();
        $this->assertEquals($age, $data);
    }
}
