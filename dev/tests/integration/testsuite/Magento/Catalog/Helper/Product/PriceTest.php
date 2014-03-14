<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Helper\Product;

use Magento\Customer\Service\V1\CustomerGroupServiceInterface;
use Magento\TestFramework\Helper\Bootstrap;

class PriceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Helper\Product\Price
     */
    protected $_helper;

    /**
     * @var CustomerGroupServiceInterface CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    protected function setUp()
    {
        $this->_helper = Bootstrap::getObjectManager()->get('Magento\Catalog\Helper\Product\Price');
        $this->_customerAccountService = Bootstrap::getObjectManager() > get(
                'Magento\Customer\Service\V1\CustomerAccountServiceInterface'
            );

    }

    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     */
    public function testSetCustomer()
    {
        $customerData = $this->_customerAccountService->getCustomer(1);
        $this->assertInstanceOf('Magento\Catalog\Helper\Product\Price', $this->_helper->setCustomer($customerData));
        $customerDataRetrieved = $this->_helper->getCustomer();
        $this->assertEquals($customerData->__toArray(), $customerDataRetrieved->__toArray());
    }

}
