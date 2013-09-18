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

class Magento_Reward_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Magento/ImportExport/_files/customer.php
     * @dataProvider saveRewardPointsDataProvider
     *
     * @param integer $pointsDelta
     * @param integer $expectedBalance
     */
    public function testSaveRewardPoints($pointsDelta, $expectedBalance)
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $customer = $objectManager->get('Magento\Core\Model\Registry')
            ->registry('_fixture/Magento\ImportExport\Customer');

        $this->_saveRewardPoints($customer, $pointsDelta);

        /** @var $reward \Magento\Reward\Model\Reward */
        $reward = Mage::getModel('Magento\Reward\Model\Reward');
        $reward->setCustomer($customer)
            ->loadByCustomer();

        $this->assertEquals($expectedBalance, $reward->getPointsBalance());
    }

    public function saveRewardPointsDataProvider()
    {
        return array(
            'points delta is not set' => array(
                '$pointsDelta' => '',
                '$expectedBalance' => null
            ),
            'points delta is positive' => array(
                '$pointsDelta' => 100,
                '$expectedBalance' => 100
            )
        );
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param mixed $pointsDelta
     */
    protected function _saveRewardPoints(\Magento\Customer\Model\Customer $customer, $pointsDelta = '')
    {
        $reward = array(
            'points_delta' => $pointsDelta
        );

        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $request Magento_TestFramework_Request */
        $request = $objectManager->get('Magento_TestFramework_Request');
        $request->setPost(
            array('reward' => $reward)
        );

        $event = new \Magento\Event(
            array(
                'request'  => $request,
                'customer' => $customer
            )
        );

        $eventObserver = new \Magento\Event\Observer(
            array('event' => $event)
        );

        $rewardObserver = Mage::getModel('Magento\Reward\Model\Observer');
        $rewardObserver->saveRewardPoints($eventObserver);
    }
}
