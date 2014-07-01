<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\CatalogRule\Model;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class RuleTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogRule\Model\Rule */
    protected $rule;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject */
    protected $registry;

    /** @var \Magento\Framework\Data\FormFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $formFactory;

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $timezone;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $collectionFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $combineFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $catalogRuleCollectionFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $productFactory;

    /** @var \Magento\Framework\Model\Resource\Iterator|\PHPUnit_Framework_MockObject_MockObject */
    protected $iterator;

    /** @var \Magento\Index\Model\Indexer|\PHPUnit_Framework_MockObject_MockObject */
    protected $indexer;

    /** @var \Magento\Customer\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $session;

    /** @var \Magento\CatalogRule\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $catalogRuleHelper;

    /** @var \Magento\Framework\App\Cache\TypeListInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $typeList;

    /** @var \Magento\Framework\Stdlib\DateTime|\PHPUnit_Framework_MockObject_MockObject */
    protected $dateTime;

    /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $productModel;

    /** @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeModel;

    /** @var \Magento\Store\Model\Website|\PHPUnit_Framework_MockObject_MockObject */
    protected $websiteModel;

    /** @var \Magento\Rule\Model\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject */
    protected $condition;

    protected function setUp()
    {
        $this->context = $this->getMock('Magento\Framework\Model\Context', array(), array(), '', false);
        $this->registry = $this->getMock('Magento\Framework\Registry');
        $this->timezone = $this->getMock('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        $this->collectionFactory = $this->getMock('Magento\Catalog\Model\Resource\Product\CollectionFactory');
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->combineFactory = $this->getMock('Magento\CatalogRule\Model\Rule\Condition\CombineFactory', ['create']);
        $this->catalogRuleCollectionFactory = $this->getMock('Magento\CatalogRule\Model\Rule\Action\CollectionFactory');
        $this->productFactory = $this->getMock('Magento\Catalog\Model\ProductFactory');
        $this->formFactory = $this->getMock('Magento\Framework\Data\FormFactory', array(), array(), '', false);
        $this->iterator = $this->getMock('Magento\Framework\Model\Resource\Iterator', array(), array(), '', false);
        $this->indexer = $this->getMock('Magento\Index\Model\Indexer', array(), array(), '', false);
        $this->session = $this->getMock('Magento\Customer\Model\Session', array(), array(), '', false);
        $this->catalogRuleHelper = $this->getMock('Magento\CatalogRule\Helper\Data', array(), array(), '', false);
        $this->typeList = $this->getMock('Magento\Framework\App\Cache\TypeListInterface');
        $this->dateTime = $this->getMock('Magento\Framework\Stdlib\DateTime');
        $this->storeModel = $this->getMock('Magento\Store\Model\Store', array('__wakeup', 'getId'), array(), '', false);
        $this->productModel = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $this->condition = $this->getMock(
            'Magento\Rule\Model\Condition\Combine',
            array(
                'setRule'
            ),
            array(),
            '',
            false
        );
        $this->websiteModel = $this->getMock(
            'Magento\Store\Model\Website',
            array(
                '__wakeup',
                'getId',
                'getDefaultStore'
            ),
            array(),
            '',
            false
        );

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->rule = $this->objectManagerHelper->getObject(
            'Magento\CatalogRule\Model\Rule',
            array(
                'context' => $this->context,
                'registry' => $this->registry,
                'formFactory' => $this->formFactory,
                'localeDate' => $this->timezone,
                'productCollectionFactory' => $this->collectionFactory,
                'storeManager' => $this->storeManager,
                'combineFactory' => $this->combineFactory,
                'actionCollectionFactory' => $this->catalogRuleCollectionFactory,
                'productFactory' => $this->productFactory,
                'resourceIterator' => $this->iterator,
                'indexer' => $this->indexer,
                'customerSession' => $this->session,
                'catalogRuleData' => $this->catalogRuleHelper,
                'cacheTypesList' => $this->typeList,
                'dateTime' => $this->dateTime
            )
        );
    }

    /**
     * @covers _getWebsitesMap
     */
    public function testCallbackValidateProduct()
    {
        $args['product'] = $this->productModel;
        $args['attributes'] = array();
        $args['idx'] = 0;
        $args['row'] = array(
            'entity_id' => '1',
            'entity_type_id' => '4',
            'attribute_set_id' => '4',
            'type_id' => 'simple',
            'sku' => 'Product',
            'has_options' => '0',
            'required_options' => '0',
            'created_at' => '2014-06-25 13:14:30',
            'updated_at' => '2014-06-25 14:37:15'
        );
        $this->storeManager->expects($this->any())->method('getWebsites')->with(true)
            ->will($this->returnValue(array($this->websiteModel)));
        $this->websiteModel->expects($this->any())->method('getId')
            ->will($this->returnValue('1'));
        $this->websiteModel->expects($this->any())->method('getDefaultStore')
            ->will($this->returnValue($this->storeModel));
        $this->storeModel->expects($this->any())->method('getId')
            ->will($this->returnValue('1'));
        $this->combineFactory->expects($this->any())->method('create')
            ->will($this->returnValue($this->condition));
        $this->condition->expects($this->any())->method('setRule')
            ->will($this->returnSelf());
        $this->rule->callbackValidateProduct($args);
    }
}
