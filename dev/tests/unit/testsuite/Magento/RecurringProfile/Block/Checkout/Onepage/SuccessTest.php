<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringProfile\Block\Checkout\Onepage;

class SuccessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
    }

    /**
     * @covers Magento\RecurringProfile\Block\Checkout\Onepage\Success::_prepareLastRecurringProfiles
     */
    public function testToHtmlPreparesRecurringProfiles()
    {
        $checkoutSessionArgs = $this->objectManager->getConstructArguments(
            'Magento\Checkout\Model\Session',
            array('storage' => new \Magento\Session\Storage('checkout'))
        );
        $checkoutSession = $this->getMock(
            'Magento\Checkout\Model\Session',
            ['getLastRecurringProfileIds'],
            $checkoutSessionArgs
        );
        $checkoutSession->expects($this->once())
            ->method('getLastRecurringProfileIds')
            ->will($this->returnValue([1, 2, 3]));
        $collection = $this->getMock(
            'Magento\RecurringProfile\Model\Resource\Profile\Collection',
            ['addFieldToFilter'],
            [],
            '',
            false
        );
        $collection->expects($this->once())->method('addFieldToFilter')
            ->with('profile_id', ['in' => [1, 2, 3]])->will($this->returnValue([]));
        $recurringProfileCollectionFactory = $this->getMock(
            'Magento\RecurringProfile\Model\Resource\Profile\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $recurringProfileCollectionFactory->expects($this->once())
            ->method('create')->will($this->returnValue($collection));

        /** @var \Magento\Checkout\Block\Onepage\Success $block */
        $block = $this->objectManager->getObject(
            'Magento\RecurringProfile\Block\Checkout\Onepage\Success',
            array(
                'checkoutSession' => $checkoutSession,
                'recurringProfileCollectionFactory' => $recurringProfileCollectionFactory,
            )
        );
        $this->assertEquals('', $block->toHtml());
    }
}
