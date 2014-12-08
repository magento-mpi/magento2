<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogPermissions\Model;

use Magento\TestFramework\Helper\ObjectManager;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogPermissions\Model\Observer
     */
    protected $_observer;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $_permissionsConfig;

    /**
     * @var \Magento\CatalogPermissions\Model\Permission\Index
     */
    protected $_permissionIndex;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    public function setUp()
    {
        $this->_storeManager = $this->getMockBuilder('Magento\Framework\StoreManagerInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_permissionsConfig = $this->_getCleanMock('\Magento\CatalogPermissions\App\ConfigInterface');
        $this->_permissionIndex = $this->_getCleanMock('\Magento\CatalogPermissions\Model\Permission\Index');

        $this->_observer = (new ObjectManager($this))->getObject('\Magento\CatalogPermissions\Model\Observer', [
            'permissionsConfig' => $this->_permissionsConfig,
            'storeManager' => $this->_storeManager,
            'customerSession' => $this->_getCleanMock('\Magento\Customer\Model\Session'),
            'permissionIndex' => $this->_permissionIndex,
            'catalogPermData' => $this->_getCleanMock('\Magento\CatalogPermissions\Helper\Data'),
            'actionFlag' => $this->_getCleanMock('\Magento\Framework\App\ActionFlag')
        ]);
    }

    /**
     * Get clean mock by class name
     *
     * @param string $className
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCleanMock($className)
    {
        return $this->getMock($className, [], [], '', false);
    }

    protected function _preparationData($step = 0)
    {
        $quoteMock = $this->_getCleanMock('\Magento\Sales\Model\Quote');

        if ($step == 0) {
            $quoteMock->expects($this->exactly(3))
                ->method('getAllItems')
                ->will($this->returnValue([]));
        } else {
            $quoteItems = $this->getMock('\Magento\Eav\Model\Entity\Collection\AbstractCollection',
                ['getProductId', 'setDisableAddToCart', 'getParentItem', 'getDisableAddToCart'],
                [],
                '',
                false
            );

            $quoteItems->expects($this->exactly(5))
                ->method('getProductId')
                ->will($this->returnValue(1));

            $quoteItems->expects($this->once())
                ->method('getParentItem')
                ->will($this->returnValue(0));

            $quoteItems->expects($this->once())
                ->method('getDisableAddToCart')
                ->will($this->returnValue(0));

            $quoteMock->expects($this->exactly(3))
                ->method('getAllItems')
                ->will($this->returnValue([$quoteItems]));
        }

        if ($step == 1) {
            $this->_permissionIndex->expects($this->exactly(1))
                ->method('getIndexForProduct')
                ->will($this->returnValue([]));
        } elseif ($step == 2) {
            $this->_permissionIndex->expects($this->exactly(1))
                ->method('getIndexForProduct')
                ->will($this->returnValue([1 => true]));
        }

        $cartMock = $this->_getCleanMock('\Magento\AdvancedCheckout\Model\Cart');
        $cartMock->expects($this->once())
            ->method('getQuote')
            ->will($this->returnValue($quoteMock));

        $eventMock = $this->getMock('\Magento\Framework\Event', ['getCart'], [], '', false);
        $eventMock->expects($this->once())
            ->method('getCart')
            ->will($this->returnValue($cartMock));

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', ['getEvent'], [], '', false);
        $observerMock->expects($this->once())
            ->method('getEvent')
            ->will($this->returnValue($eventMock));

        return $observerMock;
    }

    public function testCheckQuotePermissionsPermissionsConfigDisabled()
    {
        $this->_permissionsConfig->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(false));

        $observerMock = $this->getMock('\Magento\Framework\Event\Observer', [], [], '', false);
        $result = $this->_observer->checkQuotePermissions($observerMock);
        $this->assertInstanceOf('\Magento\CatalogPermissions\Model\Observer', $result);
    }

    /**
     * @param int $step
     * @dataProvider dataSteps
     */
    public function testCheckQuotePermissionsPermissionsConfigEnabled($step)
    {
        $this->_permissionsConfig->expects($this->once())
            ->method('isEnabled')
            ->will($this->returnValue(true));

        $observer = $this->_preparationData($step);
        $result = $this->_observer->checkQuotePermissions($observer);
        $this->assertInstanceOf('\Magento\CatalogPermissions\Model\Observer', $result);
    }

    /**
     * @return array
     */
    public function dataSteps()
    {
        return [[0], [1], [2]];
    }
}
