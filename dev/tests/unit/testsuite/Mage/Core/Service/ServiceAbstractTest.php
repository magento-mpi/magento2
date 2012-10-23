<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Service_ServiceAbstract
 */
class Mage_Core_Service_ServiceAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Service_ServiceAbstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_service;

    /**
     * Initialize service abstract for testing
     */
    protected function setUp()
    {
        $config = $this->getMockBuilder('Mage_Core_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $validatorFactory = $this->getMockBuilder('Magento_Validator_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $eventManager = $this->getMockBuilder('Mage_Core_Model_Event_Manager')
            ->disableOriginalConstructor()
            ->getMock();

        $helper = $this->getMockBuilder('Mage_Core_Helper_Data')
            ->setMethods(array('__'))
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $this->_service = $this->getMockBuilder('Mage_Core_Service_ServiceAbstract')
            ->setConstructorArgs(array(array(
                'config' => $config,
                'helper' => $helper,
                'eventManager' => $eventManager,
                'validatorFactory' => $validatorFactory
            )))
            ->getMock();
    }

    protected function tearDown()
    {
        unset($this->_service);
    }

    /**
     * Get collection mock
     *
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject|Varien_Data_Collection_Db
     */
    protected function _getCollectionMock(array $methods)
    {
        return $this->getMockBuilder('Varien_Data_Collection_Db')
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Call _prepareCollection of service
     *
     * @param Varien_Data_Collection_Db $collection
     * @param array $data
     */
    protected function _checkPrepareCollection($collection, $data)
    {
        $method = new ReflectionMethod($this->_service, '_prepareCollection');
        $method->setAccessible(true);
        $method->invokeArgs($this->_service, array($collection, $data));
    }

    /**
     * Check exceptions
     *
     * @dataProvider pagerInvalidDataProvider
     *
     * @param array $data
     * @param string $exception
     * @param string $exceptionMessage
     */
    public function testApplyCollectionExceptions(array $data, $exception, $exceptionMessage)
    {
        $collection = $this->_getCollectionMock(array('setCurPage', 'setPageSize', 'setOrder'));
        $this->setExpectedException($exception, $exceptionMessage);
        $this->_checkPrepareCollection($collection, $data);
    }

    /**
     * Data provider for testing exceptions
     *
     * @return array
     */
    public function pagerInvalidDataProvider()
    {
        return array(
            'page without limit' => array(
                array('page' => 1), 'InvalidArgumentException', 'Page number must be used with limit'
            ),
            'incorrect page' => array(
                array('page' => -1, 'limit' => 10), 'InvalidArgumentException', 'Page number is incorrect'
            ),
            'incorrect limit with page' => array(
                array('page' => 1, 'limit' => 0), 'InvalidArgumentException', 'Limit is incorrect'
            ),
            'incorrect limit without page' => array(
                array('limit' => -100), 'InvalidArgumentException', 'Limit is incorrect'
            ),
            'incorrect sort order' => array(
                array('page' => 1, 'limit' => 1, 'order' => 'name', 'dir' => 'Asd'),
                'InvalidArgumentException', 'Sort order is invalid'
            ),
            'incorrect filter' => array(
                array('filter' => array('eq' => 1)),
                'InvalidArgumentException', 'Invalid filter'
            ),
            'incorrect filter not array' => array(
                array('filter' => 1),
                'InvalidArgumentException', 'Invalid filter format'
            ),
            'incorrect filter no attribute' => array(
                array('filter' => array(
                    array('eq' => 1)
                )),
                'InvalidArgumentException', 'Invalid filter format'
            )
        );
    }

    /**
     * Test addFieldToFilter called if no addAttributeToFilter. Test RuntimeException thrown
     *
     * @expectedException RuntimeException
     * @expectedExceptionMessage Error occurred during filtering data
     */
    public function testFilterRuntimeException()
    {
        $throwException = function () {
            throw new Exception('Some exception');
        };
        $collection = $this->_getCollectionMock(array('addFieldToFilter'));
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('id', array('eq' => 10))
            ->will($this->returnCallback($throwException));
        $this->_checkPrepareCollection($collection, array(
            'filter' => array(
                array('attribute' => 'id', 'eq' => 10)
            )
        ));
    }

    /**
     * Check that pager initialized correctly
     *
     * @dataProvider pagerDataProvider
     *
     * @param array $data
     * @param int $page
     * @param int $limit
     */
    public function testApplyCollectionPager(array $data, $page, $limit)
    {
        $collection = $this->_getCollectionMock(array('setCurPage', 'setPageSize'));
        $collection->expects($this->once())
            ->method('setCurPage')
            ->with($page)
            ->will($this->returnSelf());

        $collection->expects($this->once())
            ->method('setPageSize')
            ->with($limit)
            ->will($this->returnSelf());
        $this->_checkPrepareCollection($collection, $data);
    }

    /**
     * Data provider for testApplyCollectionPager
     *
     * @return array
     */
    public function pagerDataProvider()
    {
        return array(
            array(
                array('page' => 10, 'limit' => 20), 10, 20
            ),
            array(
                array('limit' => 5), 1, 5
            )
        );
    }

    /**
     * Check order applied
     *
     * @dataProvider orderDataProvider
     *
     * @param array $data
     * @param string $order
     * @param string $dir
     */
    public function testApplyCollectionSorting(array $data, $order, $dir)
    {
        $collection = $this->_getCollectionMock(array('setOrder'));
        $collection->expects($this->once())
            ->method('setOrder')
            ->with($order, $dir)
            ->will($this->returnSelf());

        $this->_checkPrepareCollection($collection, $data);
    }

    /**
     * Data provider for testApplyCollectionSorting
     *
     * @return array
     */
    public function orderDataProvider()
    {
        return array(
            'default dir' => array(
                array('order' => 'name'), 'name', 'ASC'
            ),
            'dir asc' => array(
                array('order' => 'name', 'dir' => 'aSc'), 'name', 'aSc'
            ),
            'dir desc' => array(
                array('order' => 'name', 'dir' => 'DeSc'), 'name', 'DeSc'
            ),
        );
    }

    /**
     * Check filter applied
     *
     * @dataProvider filterDataProvider
     *
     * @param array $data
     * @param string $attribute
     * @param array $filter
     */
    public function testApplyCollectionFilter(array $data, $attribute, array $filter)
    {
        $collection = $this
            ->_getCollectionMock(array('addAttributeToFilter'));
        $collection->expects($this->once())
            ->method('addAttributeToFilter')
            ->with($attribute, $filter)
            ->will($this->returnSelf());

        $this->_checkPrepareCollection($collection, $data);
    }

    /**
     * Data provider for testApplyCollectionFilter
     *
     * @return array
     */
    public function filterDataProvider()
    {
        return array(
            array(
                array('filter' => array(array('attribute' => 'name', 'eq' => 'Test'))), 'name', array('eq' => 'Test')
            ),
            array(
                array('filter' => array(array('attribute' => 'id', 'gt' => 10))), 'id', array('gt' => 10)
            ),
        );
    }
}
