<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Catalog\Layer\Filter;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Layer\Filter\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filterItem;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_filterItemFactory;

    /**
     * @var \Magento\Framework\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_store;

    /**
     * @var \Magento\Catalog\Model\Layer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_layer;

    /**
     * @var \Magento\Catalog\Model\Layer\State|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_state;

    /**
     * @var \Magento\Catalog\Model\Resource\Layer\Filter\AttributeFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_attributeFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Layer\Filter\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_attributeItem;

    /**
     * @var \Magento\Framework\Stdlib\String|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_string;

    /**
     * @var \Magento\Search\Model\Resource\Solr\Engine|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceEngine;

    /**
     * @var \Magento\Search\Model\Resource\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productCollection;

    /**
     * @var \Magento\Search\Model\Layer\Category\Filter\Attribute
     */
    protected $_model;

    public function setUp()
    {
        $this->_filterItem = $this->getMock(
            '\Magento\Catalog\Model\Layer\Filter\Item',
            array('setFilter', 'setLabel', 'setValue', 'setCount'),
            array(),
            '',
            false
        );
        $this->_filterItem->expects($this->any())->method('setFilter')->will($this->returnSelf());
        $this->_filterItem->expects($this->any())->method('setLabel')->will($this->returnSelf());
        $this->_filterItem->expects($this->any())->method('setValue')->will($this->returnSelf());
        $this->_filterItem->expects($this->any())->method('setCount')->will($this->returnSelf());
        $this->_filterItemFactory = $this->getMock(
            '\Magento\Catalog\Model\Layer\Filter\ItemFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_filterItemFactory->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_filterItem)
        );
        $this->_store = $this->getMock('\Magento\Store\Model\Store', array(), array(), '', false);
        $this->_storeManager = $this->getMock(
            '\Magento\Framework\StoreManagerInterface',
            array(),
            array(),
            '',
            false
        );
        $this->_storeManager->expects($this->any())->method('getStore')->will($this->returnValue($this->_store));
        $this->_layer = $this->getMock('\Magento\Catalog\Model\Layer', array(), array(), '', false);
        $this->_productCollection = $this->getMock(
            '\Magento\Search\Model\Resource\Collection',
            array(),
            array(),
            '',
            false
        );
        $this->_layer->expects(
            $this->any()
        )->method(
            'getProductCollection'
        )->will(
            $this->returnValue($this->_productCollection)
        );
        $this->_state = $this->getMock('\Magento\Catalog\Model\Layer\State', array(), array(), '', false);
        $this->_layer->expects($this->any())->method('getState')->will($this->returnValue($this->_state));
        $this->_attributeFactory = $this->getMock(
            '\Magento\Catalog\Model\Resource\Layer\Filter\AttributeFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_attributeItem = $this->getMock(
            '\Magento\Catalog\Model\Resource\Layer\Filter\Attribute',
            array(),
            array(),
            '',
            false
        );
        $this->_attributeFactory->expects(
            $this->any()
        )->method(
            'create'
        )->will(
            $this->returnValue($this->_attributeItem)
        );
        $this->_string = $this->getMock('\Magento\Framework\Stdlib\String', array(), array(), '', false);
        $this->_resourceEngine = $this->getMock(
            'Magento\Search\Model\Resource\Solr\Engine',
            array(),
            array(),
            '',
            false
        );
        $tagFilter = $this->getMock('\Magento\Framework\Filter\StripTags', array(), array(), '', false);
        $tagFilter->expects($this->any())->method('filter')->will($this->returnArgument(0));

        $this->_model = new \Magento\Search\Model\Layer\Category\Filter\Attribute(
            $this->_filterItemFactory,
            $this->_storeManager,
            $this->_layer,
            $this->_attributeFactory,
            $this->_string,
            $tagFilter,
            $this->_resourceEngine
        );
    }

    /**
     * @param string $submittedValue
     * @param bool $expectedFilterApplied
     * @param string $expectedAttributeId
     * @dataProvider applyDataProvider
     */
    public function testApply($submittedValue, $expectedFilterApplied, $expectedAttributeId = null)
    {
        // Call expectations
        $options = array(
            array('label' => 'attribute_label1', 'value' => 'attribute_id1'),
            array('label' => 'attribute_label2', 'value' => 'attribute_id2')
        );
        $sourceModel = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Source\AbstractSource',
            array(),
            array(),
            '',
            false
        );
        $sourceModel->expects($this->atLeastOnce())->method('getAllOptions')->will($this->returnValue($options));

        $attributeModel = $this->getMock('\Magento\Catalog\Model\Resource\Eav\Attribute', array(), array(), '', false);
        $attributeModel->expects($this->atLeastOnce())->method('getSource')->will($this->returnValue($sourceModel));
        $this->_model->setData('attribute_model', $attributeModel);

        $this->_resourceEngine->expects(
            $this->any()
        )->method(
            'getSearchEngineFieldName'
        )->with(
            $attributeModel,
            'nav'
        )->will(
            $this->returnValue('attribute_search_field')
        );

        // Parameters for the method
        $filterBlock = $this->getMock('\Magento\Catalog\Block\Layer\Filter\Attribute', array(), array(), '', false);

        $request = $this->getMock('Zend_Controller_Request_Abstract');
        $request->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'attribute'
        )->will(
            $this->returnValue($submittedValue)
        );

        // Expectations on filtering the incoming value
        if ($expectedFilterApplied) {
            $this->_state->expects($this->once())->method('addFilter')->with($this->_filterItem);
            $this->_productCollection->expects(
                $this->once()
            )->method(
                'addFqFilter'
            )->with(
                array('attribute_search_field' => array($expectedAttributeId))
            );
        } else {
            $this->_state->expects($this->never())->method('addFilter');
            $this->_productCollection->expects($this->never())->method('addFqFilter');
        }

        $this->_model->apply($request, $filterBlock);
    }

    public function applyDataProvider()
    {
        return array(
            'existing attribute' => array('attribute_label2', true, 'attribute_id2'),
            'non-existing attribute' => array('spoofing_content', false)
        );
    }
}
