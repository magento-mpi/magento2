<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Model\Resource\Subscriber;

class SubscriberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Newsletter\Model\Resource\Subscriber\Collection
     */
    protected $_collectionModel;

    protected function setUp()
    {
        $this->_collectionModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Newsletter\Model\Resource\Subscriber\Collection');
    }

    /**
     * @magentoDataFixture Magento/Newsletter/_files/subscribers.php
     */
    public function testShowCustomerInfo()
    {
        $result = $this->_collectionModel->showCustomerInfo();
        $this->assertInstanceOf('Magento\Newsletter\Model\Resource\Subscriber\Collection',$result);
    }
}
