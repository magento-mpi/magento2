<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account\Dashboard;

class NewsletterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Newsletter block under test.
     *
     * @var Newsletter
     */
    protected $block;

    /**
     * Session model.
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Execute per test initialization.
     */
    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $this->block = $objectManager->get(
            'Magento\View\LayoutInterface'
        )->createBlock(
            'Magento\Customer\Block\Account\Dashboard\Newsletter',
            '',
            array('customerSession' => $this->customerSession)
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetSubscriptionObject()
    {
        $this->customerSession->setCustomerId(1);

        $subscriber = $this->block->getSubscriptionObject();
        $this->assertInstanceOf('Magento\Newsletter\Model\Subscriber', $subscriber);
        $this->assertFalse($subscriber->isSubscribed());
    }
}
