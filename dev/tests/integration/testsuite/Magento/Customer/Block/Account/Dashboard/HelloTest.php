<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account\Dashboard;

class HelloTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The Hello block.
     *
     * @var Hello
     */
    private $block;

    /**
     * Session model.
     *
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');

        $this->customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $this->block = $objectManager
            ->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Customer\Block\Account\Dashboard\Hello',
                '',
                ['customerSession' => $this->customerSession]
            )
            ->setTemplate('account/dashboard/hello.phtml');
    }

    /**
     * Execute per test post cleanup.
     */
    public function tearDown()
    {
        $this->customerSession->unsCustomerId();
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetCustomerName()
    {
        $this->customerSession->setCustomerId(1);
        $this->assertEquals('Firstname Lastname', $this->block->getCustomerName());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testToHtml()
    {
        $this->customerSession->setCustomerId(1);
        $html = $this->block->toHtml();
        $this->assertContains("<div class=\"block dashboard welcome\">", $html);
        $this->assertContains("<strong>Hello, Firstname Lastname!</strong>", $html);
    }
}
