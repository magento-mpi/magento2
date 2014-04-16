<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/customer.php
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testProcess()
    {
        $price = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\ProductAlert\Model\Price'
        );
        $price->setWebsiteId(1)->setId(1)->setCustomerId(1)->setProductId(1)->setPrice(10)->setSendCount(0)->setStatus(
            0
        );
        $price->isObjectNew(true);
        $price->save();
        $price->load(1);
        $stock = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\ProductAlert\Model\Stock'
        );
        $stock->setWebsiteId(1)->setId(1)->setCustomerId(1)->setProductId(1)->setSendCount(0)->setStatus(0);
        $stock->isObjectNew(true);
        $stock->save();
        $stock->load(1);
        $priceCol = $this->getMock(
            'Magento\ProductAlert\Model\Resource\Price\Collection',
            ['addWebsiteFilter', 'getSelect', 'setCustomerOrder'],
            [],
            '',
            false
        );
        $priceCol->expects($this->any())->method('addWebsiteFilter')->will($this->returnSelf());
        $priceCol->expects($this->any())->method('setCustomerOrder')->will($this->returnValue([$price]));
        $priceColFactory = $this->getMock(
            'Magento\ProductAlert\Model\Resource\Price\CollectionFactory',
            [],
            [],
            '',
            false
        );
        $priceColFactory->expects($this->any())->method('create')->will($this->returnValue($priceCol));

        $stockCol = $this->getMock(
            'Magento\ProductAlert\Model\Resource\Stock\Collection',
            ['addWebsiteFilter', 'addStatusFilter', 'getSelect', 'setCustomerOrder'],
            [],
            '',
            false
        );
        $stockCol->expects($this->any())->method('addWebsiteFilter')->will($this->returnSelf());
        $stockCol->expects($this->any())->method('addStatusFilter')->will($this->returnSelf());
        $stockCol->expects($this->any())->method('setCustomerOrder')->will($this->returnValue([$stock]));
        $stockColFactory = $this->getMock(
            'Magento\ProductAlert\Model\Resource\Stock\CollectionFactory',
            [],
            [],
            '',
            false
        );
        $stockColFactory->expects($this->any())->method('create')->will($this->returnValue($stockCol));
        $email = $this->getMock('Magento\ProductAlert\Model\Email', ['setCustomerData'], [], '', false);
        $emailFactory = $this->getMock('Magento\ProductAlert\Model\EmailFactory', [], [], '', false);
        $emailFactory->expects($this->once())->method('create')->will($this->returnValue($email));
        $coreStoreConfig = $this->getMock('Magento\Core\Model\Store\Config', [], [], '', false);
        $coreStoreConfig->expects($this->any())->method('getConfig')->will($this->returnValue(true));
        $customerAccountService = $this->getMock(
            'Magento\Customer\Service\V1\CustomerAccountServiceInterface', [], [], '', false
        );
        $customerAccountService->expects($this->atLeastOnce())->method('getCustomer')->with(1);
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\ProductAlert\Model\Observer',
            [
                'emailFactory' => $emailFactory,
                'coreStoreConfig' => $coreStoreConfig,
                'priceColFactory' => $priceColFactory,
                'stockColFactory' => $stockColFactory,
                'customerAccountService' => $customerAccountService
            ]
        );
        $model->process();
    }
}
