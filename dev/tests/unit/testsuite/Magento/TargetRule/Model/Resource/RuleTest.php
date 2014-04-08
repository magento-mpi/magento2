<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Resource\Rule;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\ObjectManager;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TargetRule\Model\Resource\Rule
     */
    protected $resourceRule;

    /**
     * @var \Magento\Module\Manager|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $moduleManager;

    /**
     * @var \Magento\Event\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $eventManager;

    /**
     * @var \Magento\Indexer\Model\CacheContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var \Magento\Model\AbstractModel|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleModel;

    /**
     * @var \Magento\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $adapter;

    /**
     * @var \Magento\App\Resource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $appResource;

    protected function setUp()
    {
        $this->moduleManager = $this->getMock('Magento\Module\Manager', [], [], '', false);
        $this->eventManager = $this->getMock('Magento\Event\ManagerInterface');
        $this->context = $this->getMock('Magento\Indexer\Model\CacheContext', [], [], '', false);
        $this->ruleModel = $this->getMock('Magento\TargetRule\Model\Rule', [], [], '', false);

        $this->adapter = $this->getMock('Magento\DB\Adapter\Pdo\Mysql',
            ['_connect', 'delete', 'describeTable', 'fetchCol', 'insert', 'lastInsertId', 'quote'], [], '', false);
        $this->adapter->expects($this->any())->method('describeTable')->will($this->returnValue([]));
        $this->adapter->expects($this->any())->method('lastInsertId')->will($this->returnValue(1));

        $this->appResource = $this->getMock('Magento\App\Resource', [], [], '', false);
        $this->appResource->expects($this->any())->method('getConnection')->will($this->returnValue($this->adapter));

        $this->resourceRule = (new ObjectManager($this))->getObject('Magento\TargetRule\Model\Resource\Rule', [
            'moduleManager' => $this->moduleManager,
            'eventManager' => $this->eventManager,
            'context' => $this->context,
            'resource' => $this->appResource,
        ]);
    }

    public function testCleanProductPagesCache()
    {
        $productIdsBeforeUnbind = [1, 2, 3];
        $matchedProductIds = [3, 4, 5];
        $productIdsForClean = [1, 2, 3, 4 => 4, 5 => 5]; // result of array_unique and array_merge

        $this->adapter->expects($this->once())->method('fetchCol')->with($this->isInstanceOf('Magento\DB\Select'))
            ->will($this->returnValue($productIdsBeforeUnbind));

        $this->ruleModel->expects($this->once())->method('getMatchingProductIds')
            ->will($this->returnValue($matchedProductIds));

        $this->moduleManager->expects($this->once())->method('isEnabled')->with('Magento_PageCache')
            ->will($this->returnValue(true));

        $this->context->expects($this->once())->method('registerEntities')
            ->with(Product::CACHE_TAG, $productIdsForClean);

        $this->eventManager->expects($this->once())->method('dispatch')
            ->with('clean_cache_by_tags', ['object' => $this->context]);

        $this->resourceRule->save($this->ruleModel);
    }

    public function testCleanProductPagesCacheIfPageCacheIsDisabled()
    {
        $this->moduleManager->expects($this->once())->method('isEnabled')->with('Magento_PageCache')
            ->will($this->returnValue(false));
        $this->context->expects($this->never())->method('registerEntities');
        $this->eventManager->expects($this->never())->method('dispatch');

        $this->resourceRule->save($this->ruleModel);
    }
}
