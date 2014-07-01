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

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $combineFactory;

    /** @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeModel;

    /** @var \Magento\Store\Model\Website|\PHPUnit_Framework_MockObject_MockObject */
    protected $websiteModel;

    /** @var \Magento\Rule\Model\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject */
    protected $condition;

    protected function setUp()
    {
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $this->combineFactory = $this->getMock('Magento\CatalogRule\Model\Rule\Condition\CombineFactory', ['create']);
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
                'storeManager' => $this->storeManager,
                'combineFactory' => $this->combineFactory
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
