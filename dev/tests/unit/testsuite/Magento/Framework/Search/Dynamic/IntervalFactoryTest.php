<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Dynamic;

use Magento\Framework\App\ScopeInterface;
use Magento\TestFramework\Helper\ObjectManager;

class IntervalFactoryTest extends \PHPUnit_Framework_TestCase
{
    const CONFIG_PATH = 'config_path';
    const INTERVAL = 'some_interval';

    /**
     * @var \Magento\Framework\ObjectManager|\PHPUnit_Framework_MockObject_MockObject
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Search\Dynamic\IntervalInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $interval;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    private $helper;

    /**
     * SetUp method
     */
    protected function setUp()
    {
        $this->helper = new ObjectManager($this);

        $this->objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager')
            ->setMethods(['create', 'get', 'configure'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->scopeConfig = $this->getMockBuilder('Magento\Framework\App\Config\ScopeConfigInterface')
            ->setMethods(['getValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->interval = $this->getMockBuilder('Magento\Framework\Search\Dynamic\IntervalInterface')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
    }

    /**
     * Test for method Create
     */
    public function testCreate()
    {
        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with(self::CONFIG_PATH, ScopeInterface::SCOPE_DEFAULT)
            ->willReturn(self::CONFIG_PATH . 't');
        $this->objectManager->expects($this->once())
            ->method('create')
            ->with(self::INTERVAL)
            ->willReturn($this->interval);

        $result = $this->factoryCreate();

        $this->assertEquals($this->interval, $result);
    }

    /**
     * @return IntervalInterface
     */
    private function factoryCreate()
    {
        /** @var \Magento\Framework\Search\Dynamic\IntervalFactory $factory */
        $factory = $this->helper->getObject(
            'Magento\Framework\Search\Dynamic\IntervalFactory',
            [
                'objectManager' => $this->objectManager,
                'scopeConfig' => $this->scopeConfig,
                'configPath' => self::CONFIG_PATH,
                'intervals' => [self::CONFIG_PATH . 't' => self::INTERVAL]
            ]
        );

        return $factory->create();
    }
}
 