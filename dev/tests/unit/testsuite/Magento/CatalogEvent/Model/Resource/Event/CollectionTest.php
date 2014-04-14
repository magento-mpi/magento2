<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\CatalogEvent\Model\Resource\Event\Collection
 */
namespace Magento\CatalogEvent\Model\Resource\Event;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Main table name
     */
    const MAIN_TABLE = 'main_table';

    /**#@+
     * Predefined store ids
     */
    const STORE_ID = 0;

    const CURRENT_STORE_ID = 1;

    /**#@-*/

    /**
     * Predefined getCheckSql result
     */
    const GET_CHECK_SQL_RESULT = 'sql_result';

    /**
     * Expected values for leftJoin method
     *
     * @var array
     */
    protected $_joinValues = array(
        2 => array(
            'name' => array('event_image' => self::MAIN_TABLE),
            'condition' => 'event_image.event_id = main_table.event_id AND event_image.store_id = %CURRENT_STORE_ID%',
            'columns' => array('image' => self::GET_CHECK_SQL_RESULT)
        ),
        3 => array(
            'name' => array('event_image_default' => self::MAIN_TABLE),
            'condition' =>
                'event_image_default.event_id = main_table.event_id AND event_image_default.store_id = %STORE_ID%',
            'columns' => array()
        )
    );

    /**
     * Replace values for store ids
     *
     * @var array
     */
    protected $_joinReplaces = array('%CURRENT_STORE_ID%' => self::CURRENT_STORE_ID, '%STORE_ID%' => self::STORE_ID);

    /**
     * Expected values for getCheckSql method
     *
     * @var array
     */
    protected $_checkSqlValues = array(
        'condition' => 'event_image.image IS NULL',
        'true' => 'event_image_default.image',
        'false' => 'event_image.image'
    );

    /**
     * @var \Magento\CatalogEvent\Model\Resource\Event\Collection
     */
    protected $_collection;

    protected function setUp()
    {
        foreach (array_keys($this->_joinValues) as $key) {
            $this->_joinValues[$key]['condition'] = str_replace(
                array_keys($this->_joinReplaces),
                array_values($this->_joinReplaces),
                $this->_joinValues[$key]['condition']
            );
        }

        $eventManager = $this->getMock('Magento\Event\ManagerInterface', array(), array(), '', false);

        $store = $this->getMock(
            'Magento\Store\Model\Store',
            array('getId', '__sleep', '__wakeup'),
            array(),
            '',
            false
        );
        $store->expects($this->once())->method('getId')->will($this->returnValue(self::CURRENT_STORE_ID));

        $storeManager = $this->getMock('Magento\Store\Model\StoreManager', array('getStore'), array(), '', false);
        $storeManager->expects($this->once())->method('getStore')->will($this->returnValue($store));

        $select = $this->getMock('Magento\DB\Select', array('joinLeft', 'from', 'columns'), array(), '', false);
        foreach ($this->_joinValues as $key => $arguments) {
            $select->expects(
                $this->at($key)
            )->method(
                'joinLeft'
            )->with(
                $arguments['name'],
                $arguments['condition'],
                $arguments['columns']
            )->will(
                $this->returnSelf()
            );
        }

        $adapter = $this->getMock(
            'Magento\DB\Adapter\Pdo\Mysql',
            array('select', 'quoteInto', 'getCheckSql', 'quote'),
            array(),
            '',
            false
        );
        $adapter->expects($this->once())->method('select')->will($this->returnValue($select));
        $adapter->expects($this->exactly(5))->method('quoteInto')->will(
            $this->returnCallback(
                function ($text, $value) {
                    return str_replace('?', $value, $text);
                }
            )
        );
        $adapter->expects(
            $this->exactly(1)
        )->method(
            'getCheckSql'
        )->will(
            $this->returnCallback(array($this, 'verifyGetCheckSql'))
        );

        $adapter->expects(
            $this->exactly(1)
        )->method(
            'getCheckSql'
        )->will(
            $this->returnCallback(array($this, 'verifyGetCheckSql'))
        );

        $resource = $this->getMockForAbstractClass(
            'Magento\Model\Resource\Db\AbstractDb',
            array(),
            '',
            false,
            true,
            true,
            array('getReadConnection', 'getMainTable', 'getTable', '__wakeup')
        );
        $resource->expects($this->once())->method('getReadConnection')->will($this->returnValue($adapter));
        $resource->expects($this->once())->method('getMainTable')->will($this->returnValue(self::MAIN_TABLE));
        $resource->expects($this->exactly(3))->method('getTable')->will($this->returnValue(self::MAIN_TABLE));

        $fetchStrategy = $this->getMockForAbstractClass('Magento\Data\Collection\Db\FetchStrategyInterface');
        $entityFactory = $this->getMock('Magento\Core\Model\EntityFactory', array(), array(), '', false);
        $logger = $this->getMock('Magento\Logger', array(), array(), '', false);
        $dateTime = $this->getMock('Magento\Stdlib\DateTime', null, array(), '', true);

        $this->_collection = $this->getMock(
            'Magento\CatalogEvent\Model\Resource\Event\Collection',
            array('setModel'),
            array($entityFactory, $logger, $fetchStrategy, $eventManager, $storeManager, $dateTime, null, $resource)
        );
    }

    protected function tearDown()
    {
        $this->_collection = null;
    }

    /**
     * Callback and verify getCheckSql method arguments
     *
     * @param string $condition     expression
     * @param string $true          true value
     * @param string $false         false value
     * @return string
     */
    public function verifyGetCheckSql($condition, $true, $false)
    {
        $this->assertEquals($this->_checkSqlValues['condition'], $condition);
        $this->assertEquals($this->_checkSqlValues['true'], $true);
        $this->assertEquals($this->_checkSqlValues['false'], $false);

        return self::GET_CHECK_SQL_RESULT;
    }

    public function testAddImageData()
    {
        $this->assertInstanceOf(
            'Magento\CatalogEvent\Model\Resource\Event\Collection',
            $this->_collection->addImageData()
        );
    }
}
