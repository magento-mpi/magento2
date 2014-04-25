<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model;

use Magento\Customer\Service\V1\Data\Customer;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/Customer/_files/import_export/customer.php
     * @dataProvider saveRewardPointsDataProvider
     *
     * @param integer $pointsDelta
     * @param integer $expectedBalance
     */
    public function testSaveRewardPoints($pointsDelta, $expectedBalance)
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $objectManager->get('Magento\Framework\Registry')
            ->registry('_fixture/Magento_ImportExport_Customer');

        /** @var CustomerAccountServiceInterface $customerAccountService */
        $customerAccountService = $objectManager->get('Magento\Customer\Service\V1\CustomerAccountServiceInterface');

        $this->_saveRewardPoints($customerAccountService->getCustomer($customer->getId()), $pointsDelta);

        /** @var $reward \Magento\Reward\Model\Reward */
        $reward = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Reward\Model\Reward');
        $reward->setCustomer($customer)->loadByCustomer();

        $this->assertEquals($expectedBalance, $reward->getPointsBalance());
    }

    public function saveRewardPointsDataProvider()
    {
        return array(
            'points delta is not set' => array('$pointsDelta' => '', '$expectedBalance' => null),
            'points delta is positive' => array('$pointsDelta' => 100, '$expectedBalance' => 100)
        );
    }

    /**
     * @param Customer $customer
     * @param mixed $pointsDelta
     */
    protected function _saveRewardPoints(Customer $customer, $pointsDelta = '')
    {
        $reward = array('points_delta' => $pointsDelta);

        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        $request->setPost(array('reward' => $reward));

        $event = new \Magento\Framework\Event(array('request' => $request, 'customer' => $customer));

        $eventObserver = new \Magento\Framework\Event\Observer(array('event' => $event));

        $rewardObserver = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Reward\Model\Observer'
        );
        $rewardObserver->saveRewardPoints($eventObserver);
    }
}
