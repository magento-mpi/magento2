<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Model\Session;

class SendTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Send
     */
    protected $block;

    /**
     * @var Session
     */
    protected $customerSession;

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('frontend');

        $this->customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $this->block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Catalog\Block\Product\Send',
                '',
                ['customerSession' => $this->customerSession]
            );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetUserName()
    {
        $this->customerSession->setCustomerId(1);
        $this->assertEquals('Firstname Lastname', $this->block->getUserName());
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testGetEmail()
    {
        $this->customerSession->setCustomerId(1);
        $this->assertEquals('customer@example.com', $this->block->getEmail());
    }
}
