<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PersistentHistory\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $modulesReaderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var \Magento\PersistentHistory\Helper\Data
     */
    protected $subject;

    protected function setUp()
    {
        /** @var \Magento\TestFramework\Helper\ObjectManager */
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $contextMock = $this->getMock('\Magento\Framework\App\Helper\Context', [], [], '', false);
        $this->modulesReaderMock = $this->getMock('\Magento\Framework\Module\Dir\Reader', [], [], '', false);
        $this->scopeConfigMock = $this->getMock('\Magento\Framework\App\Config\ScopeConfigInterface');
        $this->subject = $objectManager->getObject(
            '\Magento\PersistentHistory\Helper\Data',
            [
                'context' => $contextMock,
                'modulesReader' => $this->modulesReaderMock,
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }

    public function testGetPersistentConfigFilePath()
    {
        $this->modulesReaderMock->expects($this->once())
            ->method('getModuleDir')
            ->with('etc', 'Magento_PersistentHistory');
        $this->assertEquals('/persistent.xml', $this->subject->getPersistentConfigFilePath());
    }

    public function testIsWishlistPersist()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'persistent/options/wishlist',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(true));
        $this->assertTrue($this->subject->isWishlistPersist($storeMock));
    }

    public function testIsOrderedItemsPersist()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'persistent/options/recently_ordered',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(true));
        $this->assertTrue($this->subject->isOrderedItemsPersist($storeMock));
    }

    public function testIsCompareProductsPersist()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'persistent/options/compare_current',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(true));
        $this->assertTrue($this->subject->isCompareProductsPersist($storeMock));
    }

    public function testIsComparedProductsPersist()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'persistent/options/compare_history',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(true));
        $this->assertTrue($this->subject->isComparedProductsPersist($storeMock));
    }

    public function testIsViewedProductsPersist()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'persistent/options/recently_viewed',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(true));
        $this->assertTrue($this->subject->isViewedProductsPersist($storeMock));
    }

    public function testIsCustomerAndSegmentsPersist()
    {
        $storeMock = $this->getMock('\Magento\Store\Model\Store', [], [], '', false);
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(
                'persistent/options/customer',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeMock
            )
            ->will($this->returnValue(true));
        $this->assertTrue($this->subject->isCustomerAndSegmentsPersist($storeMock));
    }
}
 