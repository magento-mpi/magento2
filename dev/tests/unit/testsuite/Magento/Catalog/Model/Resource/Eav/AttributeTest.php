<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource\Eav;

class AttributeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Resource\Eav\Attribute
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor
     */
    protected $_processor;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Eav\Processor
     */
    protected $_eavProcessor;

    public function setUp()
    {
        $this->_processor = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Flat\Processor',
            array(),
            array(),
            '',
            false
        );

        $this->_eavProcessor = $this->getMock(
            '\Magento\Catalog\Model\Indexer\Product\Eav\Processor',
            array(),
            array(),
            '',
            false
        );

        $eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface', array(), array(), '', false);

        $cacheInterfaceMock = $this->getMock('Magento\Framework\App\CacheInterface', array(), array(), '', false);

        $actionValidatorMock = $this->getMock(
            '\Magento\Framework\Model\ActionValidator\RemoveAction', array(), array(), '', false
        );
        $actionValidatorMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));

        $contextMock = $this->getMock(
            '\Magento\Framework\Model\Context',
            array('getEventDispatcher', 'getCacheManager', 'getActionValidator'), array(), '', false
        );

        $contextMock->expects($this->any())->method('getEventDispatcher')->will($this->returnValue($eventManagerMock));
        $contextMock->expects($this->any())->method('getCacheManager')->will($this->returnValue($cacheInterfaceMock));
        $contextMock->expects($this->any())->method('getActionValidator')
            ->will($this->returnValue($actionValidatorMock));

        $dbAdapterMock = $this->getMock('Magento\Framework\DB\Adapter\Pdo\Mysql', array(), array(), '', false);

        $dbAdapterMock->expects($this->any())->method('getTransactionLevel')->will($this->returnValue(1));

        $resourceMock = $this->getMock(
            'Magento\Framework\Model\Resource\AbstractResource',
            array('_construct', '_getReadAdapter', '_getWriteAdapter', 'getIdFieldName',
                'save', 'saveInSetIncluding', 'isUsedBySuperProducts', 'delete'),
            array(), '', false
        );

        $resourceMock->expects($this->any())->method('_getWriteAdapter')->will($this->returnValue($dbAdapterMock));

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManager->getObject(
                '\Magento\Catalog\Model\Resource\Eav\Attribute',
                [
                    'context' => $contextMock,
                    'productFlatIndexerProcessor' => $this->_processor,
                    'indexerEavProcessor' => $this->_eavProcessor,
                    'resource' => $resourceMock,
                    'data' => array('id' => 1)
                ]
        );
    }

    public function testIndexerAfterSaveAttribute()
    {
        $this->_processor->expects($this->once())->method('markIndexerAsInvalid');

        $this->_model->setData(array('id' => 2, 'used_in_product_listing' => 1));

        $this->_model->afterSave();
    }

    public function testIndexerAfterSaveScopeChangeAttribute()
    {
        $this->_processor->expects($this->once())->method('markIndexerAsInvalid');

        $this->_model->setOrigData('is_global', \Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_STORE);
        $this->_model->setOrigData('used_in_product_listing', 1);
        $this->_model->setIsGlobal(\Magento\Catalog\Model\Resource\Eav\Attribute::SCOPE_GLOBAL);
        $this->_model->afterSave();

    }

    public function testIndexerAfterDeleteAttribute()
    {
        $this->_processor->expects($this->once())->method('markIndexerAsInvalid');
        $this->_model->setOrigData('id', 2);
        $this->_model->setOrigData('used_in_product_listing', 1);
        $this->_model->afterDeleteCommit();
    }
}
