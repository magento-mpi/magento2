<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
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

    public function setUp()
    {
        $this->_processor = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Flat\Processor',
            array(),
            array(),
            '',
            false
        );

        $eventManagerMock = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);

        $cacheInterfaceMock = $this->getMock('Magento\App\CacheInterface', array(), array(), '', false);

        $actionValidatorMock = $this->getMock(
            '\Magento\Model\ActionValidator\RemoveAction', array(), array(), '', false
        );
        $actionValidatorMock->expects($this->any())->method('isAllowed')->will($this->returnValue(true));

        $contextMock = $this->getMock(
            '\Magento\Model\Context',
            array('getEventDispatcher', 'getCacheManager', 'getActionValidator'), array(), '', false
        );

        $contextMock->expects($this->any())->method('getEventDispatcher')->will($this->returnValue($eventManagerMock));
        $contextMock->expects($this->any())->method('getCacheManager')->will($this->returnValue($cacheInterfaceMock));
        $contextMock->expects($this->any())->method('getActionValidator')
            ->will($this->returnValue($actionValidatorMock));

        $dbAdapterMock = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array(), array(), '', false);

        $dbAdapterMock->expects($this->any())->method('getTransactionLevel')->will($this->returnValue(1));

        $resourceMock = $this->getMock(
            'Magento\Model\Resource\AbstractResource',
            array('_construct', '_getReadAdapter', '_getWriteAdapter', 'getIdFieldName',
                'save', 'saveInSetIncluding', 'isUsedBySuperProducts', 'delete'),
            array(), '', false
        );

        $resourceMock->expects($this->any())->method('_getWriteAdapter')->will($this->returnValue($dbAdapterMock));

        $this->_model = new \Magento\Catalog\Model\Resource\Eav\Attribute(
            $contextMock,
            $this->getMock('Magento\Registry', array(), array(), '', false),
            $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false),
            $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false),
            $this->getMock('Magento\Eav\Model\Entity\TypeFactory', array(), array(), '', false),
            $this->getMock('Magento\Store\Model\StoreManagerInterface', array(), array(), '', false),
            $this->getMock('Magento\Eav\Model\Resource\Helper', array(), array(), '', false),
            $this->getMock('Magento\Validator\UniversalFactory', array(), array(), '', false),
            $this->getMock('Magento\Stdlib\DateTime\TimezoneInterface', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\Product\ReservedAttributeList', array(), array(), '', false),
            $this->getMock('Magento\Locale\ResolverInterface', array(), array(), '', false),
            $this->getMock('Magento\Index\Model\Indexer', array(), array(), '', false),
            $this->_processor,
            $this->getMock('\Magento\Catalog\Helper\Product\Flat\Indexer', array(), array(), '', false),
            $this->getMock('\Magento\Catalog\Model\Attribute\LockValidatorInterface'),
            $resourceMock,
            $this->getMock('\Magento\Data\Collection\Db', array(), array(), '', false),
            array('id' => 1)
        );
    }

    public function testIndexerAfterSaveAttribute()
    {
        $this->_processor->expects($this->once())->method('markIndexerAsInvalid');

        $this->_model->setData(array('id' => 2, 'used_in_product_listing' => 1));

        $this->_model->save();
    }

    public function testIndexerAfterDeleteAttribute()
    {
        $this->_processor->expects($this->once())->method('markIndexerAsInvalid');

        $this->_model->delete();
    }
}
