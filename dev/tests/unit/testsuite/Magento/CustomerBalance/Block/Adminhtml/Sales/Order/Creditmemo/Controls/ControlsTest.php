<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Creditmemo\Controls;

use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;
use Magento\TestFramework\Helper\ObjectManager;

/**
 * Unit tests for Refunds balance through admin page class.
 */
class ControlsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Class name for the class to be tested
     *
     * @var string
     */
    protected $controlsClass = 'Magento\CustomerBalance\Block\Adminhtml\Sales\Order\Creditmemo\Controls';

    /**
     * Class name for credit memo controls mock
     *
     * @var string
     */
    protected $controlsMock = 'CreditMemoControls';

    /**
     * Class name for the Credit memo mock
     *
     * @var string
     */
    protected $creditMemoClass = 'Magento\Sales\Model\Order\CreditmemoMock';

    /**
     * Class name for the context class
     *
     * @var string
     */
    protected $contextClass = 'Magento\View\Element\Template\Context';

    /**
     * Class name for the registry class
     *
     * @var string
     */
    protected $registryClass = 'Magento\Core\Model\Registry';

    /**
     * Name of mocked method
     *
     * @var string
     */
    protected $getRewardCurrencyAmount = 'getRewardCurrencyAmount';

    /**
     * Name of mocked method
     *
     * @var string
     */
    protected $getCustomerBalanceReturnMax = 'getCustomerBalanceReturnMax';

    /**
     * Name of mocked method
     *
     * @var string
     */
    protected $registry = 'registry';

    /**
     * Name of mocked method
     *
     * @var string
     */
    protected $getCreditmemo = '_getCreditmemo';

    /**
     * Holds mock of credit Memo class
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockCreditMemo;

    /**
     * Holds mock of context class
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockContext;

    /**
     * Holds mock of registry class
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockRegistry;

    /**
     * Holds mock of Credit Memo Controls class
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $mockMainClass;

    /**
     * Setup the needed mocks for these tests
     */
    public function setUp()
    {
        // Create mock Context object
        $this->mockContext = $this->getMockBuilder($this->contextClass)->disableOriginalConstructor()->getMock();
        // Create mock Credit memo class and prepare to override two methods
        $this->mockCreditMemo = $this->getMockBuilder(
            $this->creditMemoClass
        )->disableOriginalConstructor()->setMethods(
            array($this->getRewardCurrencyAmount, $this->getCustomerBalanceReturnMax)
        )->getMock();
        // Create mock registry and set it up to return mock credit memo class
        $this->mockRegistry = $this->getMockBuilder(
            $this->registryClass
        )->disableOriginalConstructor()->setMethods(
            array($this->registry)
        )->getMock();
        $this->mockRegistry->expects(
            $this->any()
        )->method(
            $this->registry
        )->will(
            $this->returnValue($this->mockCreditMemo)
        );
        // Create mock of main class feeding it the mocks created above.
        $this->mockMainClass = $this->getMock(
            $this->controlsClass,
            array($this->getCreditmemo),
            array($this->mockContext, $this->mockRegistry),
            $this->controlsMock,
            true
        );
    }

    /**
     * Basic test of calculating a return value with reward currency
     */
    public function testGetReturnValue()
    {
        $this->mockCreditMemo->expects(
            $this->any()
        )->method(
            $this->getRewardCurrencyAmount
        )->will(
            $this->returnValue(10)
        );

        $this->mockCreditMemo->expects(
            $this->any()
        )->method(
            $this->getCustomerBalanceReturnMax
        )->will(
            $this->returnValue(100)
        );

        $this->assertEquals(90, $this->mockMainClass->getReturnValue(), "Final refund amount wrong");
    }

    /**
     * Test calculating return without reward balance
     */
    public function testGetReturnValueWithNoRewardBalance()
    {
        $this->mockCreditMemo->expects(
            $this->any()
        )->method(
            $this->getRewardCurrencyAmount
        )->will(
            $this->returnValue(0)
        );

        $this->mockCreditMemo->expects(
            $this->any()
        )->method(
            $this->getCustomerBalanceReturnMax
        )->will(
            $this->returnValue(100)
        );

        $this->assertEquals(100, $this->mockMainClass->getReturnValue(), "Final refund amount wrong");
    }

    /**
     * Test getting return balance with invalid rewards.
     */
    public function testGetReturnValueWithInvalidRewardBalance()
    {
        $this->mockCreditMemo->expects(
            $this->any()
        )->method(
            $this->getRewardCurrencyAmount
        )->will(
            $this->returnValue(200)
        );

        $this->mockCreditMemo->expects(
            $this->any()
        )->method(
            $this->getCustomerBalanceReturnMax
        )->will(
            $this->returnValue(100)
        );

        $this->assertEquals(100, $this->mockMainClass->getReturnValue(), "Final refund amount wrong");
    }
}
