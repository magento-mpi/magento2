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
            'Magento\Catalog\Model\Indexer\Product\Flat\Processor', array(), array(), '', false
        );

        $eventManagerMock = $this->getMock(
            'Magento\Event\ManagerInterface',
            array(), array(), '', false
        );

        $cacheInterfaceMock = $this->getMock(
            'Magento\App\CacheInterface',
            array(), array(), '', false
        );

        $contextMock = $this->getMock(
            '\Magento\Core\Model\Context',
            array('getEventDispatcher', 'getCacheManager'), array(), '', false
        );

        $contextMock->expects($this->any())->method('getEventDispatcher')->will($this->returnValue($eventManagerMock));
        $contextMock->expects($this->any())->method('getCacheManager')->will($this->returnValue($cacheInterfaceMock));

        $dbAdapterMock = $this->getMock(
            'Magento\DB\Adapter\Pdo\Mysql',
            array(), array(), '', false
        );

        $dbAdapterMock->expects($this->any())->method('getTransactionLevel')->will($this->returnValue(1));

        $resourceMock = $this->getMock(
            'Magento\Core\Model\Resource\AbstractResource',
            array('_construct', '_getReadAdapter', '_getWriteAdapter', 'getIdFieldName',
                'save', 'saveInSetIncluding', 'isUsedBySuperProducts', 'delete'),
            array(), '', false
        );

        $resourceMock->expects($this->any())
            ->method('_getWriteAdapter')
            ->will($this->returnValue($dbAdapterMock));

        $this->_model = new \Magento\Catalog\Model\Resource\Eav\Attribute(
            $contextMock,
            $this->getMock('Magento\Core\Model\Registry', array(), array(), '', false),
            $this->getMock('Magento\Core\Helper\Data', array(), array(), '', false),
            $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false),
            $this->getMock('Magento\Eav\Model\Entity\TypeFactory', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\StoreManagerInterface', array(), array(), '', false),
            $this->getMock('Magento\Eav\Model\Resource\Helper', array(), array(), '', false),
            $this->getMock('Magento\Validator\UniversalFactory', array(), array(), '', false),
            $this->getMock('Magento\Core\Model\LocaleInterface', array(), array(), '', false),
            $this->getMock('Magento\Catalog\Model\ProductFactory', array(), array(), '', false),
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
        $this->_processor->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->_model->setData(array('id' => 2, 'used_in_product_listing' => 1));

        $this->_model->save();
    }

    public function testIndexerAfterDeleteAttribute()
    {
        $this->_processor->expects($this->once())
            ->method('markIndexerAsInvalid');

        $this->_model->delete();
    }
}
