<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Resource;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\ObjectManager;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Resource\Rule
     */
    protected $resourceRule;

    /**
     * @var \Magento\Framework\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * @var \Magento\Indexer\Model\CacheContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Framework\Model\AbstractModel|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleModel;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapter;

    /**
     * @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appResource;

    protected function setUp()
    {
        $this->moduleManager = $this->getMock('Magento\Framework\Module\Manager', [], [], '', false);
        $this->eventManager = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $this->context = $this->getMock('Magento\Indexer\Model\CacheContext', [], [], '', false);
        $this->ruleModel = $this->getMock('Magento\TargetRule\Model\Rule', [], [], '', false);

        $this->adapter = $this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql',
            ['_connect', 'delete', 'insertOnDuplicate'], [], '', false);
        $this->adapter->expects($this->any())->method('describeTable')->will($this->returnValue([]));
        $this->adapter->expects($this->any())->method('lastInsertId')->will($this->returnValue(1));

        $this->appResource = $this->getMock('Magento\Framework\App\Resource', [], [], '', false);
        $this->appResource->expects($this->any())->method('getConnection')->will($this->returnValue($this->adapter));

        $this->resourceRule = (new ObjectManager($this))->getObject('Magento\TargetRule\Model\Resource\Rule', [
            'moduleManager' => $this->moduleManager,
            'eventManager' => $this->eventManager,
            'context' => $this->context,
            'resource' => $this->appResource,
        ]);
    }

    public function testSaveCustomerSegments()
    {
        $ruleId = 1;
        $segmentIds = array(1, 2);

        $this->adapter->expects($this->at(2))
            ->method('insertOnDuplicate')
            ->will($this->returnSelf());

        $this->adapter->expects($this->once())
            ->method('delete')
            ->with($this->resourceRule->getTable('magento_targetrule_customersegment'))
            ->will($this->returnSelf());

        $this->resourceRule->saveCustomerSegments($ruleId, $segmentIds);
    }

    public function testCleanCachedDataByProductIds()
    {
        $productIds = array (1, 2, 3);
        $this->moduleManager->expects($this->once())
            ->method('isEnabled')
            ->with('Magento_PageCache')
            ->will($this->returnValue(true));

        $this->context->expects($this->once())
            ->method('registerEntities')
            ->with(Product::CACHE_TAG, $productIds)
            ->will($this->returnSelf());

        $this->eventManager->expects($this->once())
            ->method('dispatch')
            ->with('clean_cache_by_tags', array('object' => $this->context))
            ->will($this->returnSelf());

        $this->resourceRule->cleanCachedDataByProductIds($productIds);
    }

    public function testBindRuleToEntity()
    {
        $this->appResource->expects($this->any())
            ->method('getTableName')
            ->with('magento_targetrule_product')
            ->will($this->returnValue('magento_targetrule_product'));

        $this->adapter->expects($this->any())
            ->method('insertOnDuplicate')
            ->with('magento_targetrule_product', [['product_id' => 1, 'rule_id' => 1]], ['rule_id']);

        $this->adapter->expects($this->never())
            ->method('beginTransaction');
        $this->adapter->expects($this->never())
            ->method('commit');
        $this->adapter->expects($this->never())
            ->method('rollback');

        $this->resourceRule->bindRuleToEntity([1], [1], 'product');
    }
}
