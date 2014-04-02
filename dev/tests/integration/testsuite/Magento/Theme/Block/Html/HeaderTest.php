<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Html;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test for Magento\Theme\Block\Html\Header
 *
 * @magentoDataFixture Magento/Customer/_files/customer.php
 */
class HeaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Theme\Block\Html\Header
     */
    protected $block;

    /**
     * @var \Magento\App\Http\Context
     */
    protected $context;

    /**
     * Setup SUT
     */
    protected function setUp()
    {
        Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('frontend');
        $objectManager = Bootstrap::getObjectManager();
        $this->context = $objectManager->get('Magento\App\Http\Context');
        $this->context->setValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH, false, false);

        //Setup customer session
        $customerIdFromFixture = 1;
        $customerSession = Bootstrap::getObjectManager()->create('Magento\Customer\Model\Session');
        /**
         * @var $customerService \Magento\Customer\Service\V1\CustomerAccountServiceInterface
         */
        $customerService = Bootstrap::getObjectManager()
            ->create('Magento\Customer\Service\V1\CustomerAccountServiceInterface');
        $customerData = $customerService->getCustomer($customerIdFromFixture);
        $customerSession->setCustomerDataObject($customerData);

        //Create block and inject customer session
        /**
         * @var \Magento\View\LayoutInterface $layout
         */
        $layout = $objectManager->get('Magento\View\LayoutInterface');
        $this->block = $layout->createBlock(
            'Magento\Theme\Block\Html\Header',
            '',
            ['customerSession' => $customerSession]
        );

    }

    /**
     * Test default welcome message when customer is not logged in
     */
    public function testGetWelcomeDefault()
    {
        $this->assertEquals('Default welcome msg!', $this->block->getWelcome());
    }

    /**
     * Test welcome message when customer is logged in
     */
    public function testGetWelcomeLoggedIn()
    {
        $this->context->setValue(\Magento\Customer\Helper\Data::CONTEXT_AUTH, true, false);
        $this->assertEquals('Welcome, Firstname Lastname!', $this->block->getWelcome());
    }

}
