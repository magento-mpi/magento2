<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Observer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

class SaveRewardPointsTest extends \PHPUnit_Framework_TestCase
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
     * @param CustomerInterface $customer
     * @param mixed $pointsDelta
     */
    protected function _saveRewardPoints(CustomerInterface $customer, $pointsDelta = '')
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
            'Magento\Reward\Model\Observer\SaveRewardPoints'
        );
        $rewardObserver->execute($eventObserver);
    }
}
