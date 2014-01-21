<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Adminhtml;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Core\Model\LocaleInterface;
use Magento\Customer\Service\V1\CustomerService;
use Magento\Stdlib\DateTime;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\View\LayoutInterface;

/**
 * Magento\Customer\Block\Adminhtml\Grid
 *
 * @magentoAppArea adminhtml
 */
class GridTest extends \PHPUnit_Framework_TestCase
{
    /** @var LayoutInterface */
    private $layout;

    /** @var CustomerService */
    private $customerService;

    /** @var LocaleInterface */
    private $locale;


    public function setUp()
    {
        $this->layout = Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Layout',
            ['area' => FrontNameResolver::AREA_CODE]
        );
        $this->customerService = Bootstrap::getObjectManager()->create(
            'Magento\Customer\Service\V1\CustomerService'
        );
        $this->locale = Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\LocaleInterface'
        );
    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Customer/_files/customer_address.php
     * @magentoDataFixture Magento/Customer/_files/customer_no_address.php
     */
    public function testGetCsv()
    {
        /** @var $block Grid */
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Grid', 'block');
        $csv = $block->getCsv();
        $customer5ca = $this->formatDatetime($this->customerService->getCustomer(5)->getCreatedAt());
        $customer1ca = $this->formatDatetime($this->customerService->getCustomer(1)->getCreatedAt());

        $expected = <<<EOT
"ID","Name","Email","Group","Phone","ZIP","Country","State/Province","Customer Since","Web Site"
"5","Firstname Lastname","customer5@example.com","General","","","","","$customer5ca","Main Website"
"1","Firstname Lastname","customer@example.com","General","3468676","75477","United States","Alabama","$customer1ca","Main Website"

EOT;
        $this->assertEquals($expected, $csv);
    }

    public function testGetCsvNoData()
    {
        /** @var $block Grid */
        $block = $this->layout->createBlock('Magento\Customer\Block\Adminhtml\Grid', 'block');
        $csv = $block->getCsv();

        $expected = <<<EOT
"ID","Name","Email","Group","Phone","ZIP","Country","State/Province","Customer Since","Web Site"

EOT;
        $this->assertEquals($expected, $csv);
    }

    private function formatDatetime($date)
    {
        $format = $this->locale->getDateTimeFormat(
            LocaleInterface::FORMAT_TYPE_MEDIUM
        );
        return $this->locale->date($date, DateTime::DATETIME_INTERNAL_FORMAT)->toString($format);
    }
}
