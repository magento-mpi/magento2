<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab;

use Magento\TestFramework\Helper\Bootstrap;
use Magento\Customer\Controller\Adminhtml\Index;

/**
 * Class NewsletterTest
 *
 * @magentoAppArea adminhtml
 */
class NewsletterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Newsletter
     */
    private $block;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    private $coreRegistry;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get('Magento\App\State')->setAreaCode('adminhtml');

        $this->coreRegistry = $objectManager->get('Magento\Core\Model\Registry');
        $this->block = $objectManager->get('Magento\View\LayoutInterface')
            ->createBlock(
                'Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter',
                '',
                [
                    'registry' => $this->coreRegistry
                ]
            )
            ->setTemplate('tab/newsletter.phtml');
    }

    /**
     * Execute post test cleanup
     */
    public function tearDown()
    {
        $this->coreRegistry->unregister(Index::REGISTRY_CURRENT_CUSTOMER_ID);
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testToHtml()
    {
        $this->coreRegistry->register(Index::REGISTRY_CURRENT_CUSTOMER_ID, 1);
        $html = $this->block->initForm()->toHtml();

        $this->assertStringStartsWith("<div class=\"entry-edit\">", $html);
        $this->assertContains("<span>Newsletter Information</span>", $html);
        $this->assertContains("type=\"checkbox\"", $html);
        $this->assertNotContains("checked=\"checked\"", $html);
        $this->assertContains("<span>Subscribed to Newsletter</span>", $html);
        $this->assertContains(">No Newsletter Found<", $html);
    }
}
