<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Resource\Reward\History;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Resource\Reward\History\Collection
     */
    protected $_collection;

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_collection = $objectManager->create('Magento\Reward\Model\Resource\Reward\History\Collection');
    }

    /**
     * @magentoDataFixture Magento/Reward/_files/history.php
     */
    public function testLoadUnserializeItems()
    {
        $this->_collection->load();
        $this->assertEquals(1, $this->_collection->count());
        /** @var \Magento\Reward\Model\Reward\History $rewardHistory */
        $rewardHistory = $this->_collection->getFirstItem();
        $this->assertSame(['email' => 'test@example.com'], $rewardHistory->getAdditionalData());
    }
}
