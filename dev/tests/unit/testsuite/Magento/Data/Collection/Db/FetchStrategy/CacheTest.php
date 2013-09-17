<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Data_Collection_Db_FetchStrategy_CacheTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Data_Collection_Db_FetchStrategy_Cache
     */
    private $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_cache;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_fetchStrategy;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_select;

    /**
     * @var array
     */
    private $_fixtureData = array(
        array('column_one' => 'row_one_value_one', 'column_two' => 'row_one_value_two'),
        array('column_one' => 'row_two_value_one', 'column_two' => 'row_two_value_two'),
    );

    protected function setUp()
    {
        $this->_select = $this->getMock('Zend_Db_Select', array('assemble'), array(), '', false);
        $this->_select
            ->expects($this->once())
            ->method('assemble')
            ->will($this->returnValue('SELECT * FROM fixture_table'))
        ;

        $this->_cache = $this->getMockForAbstractClass('Magento_Cache_FrontendInterface');
        $this->_fetchStrategy = $this->getMockForAbstractClass('Magento_Data_Collection_Db_FetchStrategyInterface');

        $this->_object = new Magento_Data_Collection_Db_FetchStrategy_Cache(
            $this->_cache, $this->_fetchStrategy, 'fixture_', array('fixture_tag_one', 'fixture_tag_two'), 86400
        );
    }

    protected function tearDown()
    {
        $this->_object = null;
        $this->_cache = null;
        $this->_fetchStrategy = null;
        $this->_select = null;
    }

    public function testFetchAllCached()
    {
        $this->_cache
            ->expects($this->once())
            ->method('load')
            ->with('fixture_06a6b0cfd83bf997e76b1b403df86569')
            ->will($this->returnValue(serialize($this->_fixtureData)))
        ;
        $this->_fetchStrategy
            ->expects($this->never())
            ->method('fetchAll')
        ;
        $this->_cache
            ->expects($this->never())
            ->method('save')
        ;
        $this->assertEquals($this->_fixtureData, $this->_object->fetchAll($this->_select, array()));
    }

    public function testFetchAllDelegation()
    {
        $cacheId = 'fixture_06a6b0cfd83bf997e76b1b403df86569';
        $bindParams = array('param_one' => 'value_one', 'param_two' => 'value_two');
        $this->_cache
            ->expects($this->once())
            ->method('load')
            ->with($cacheId)
            ->will($this->returnValue(false))
        ;
        $this->_fetchStrategy
            ->expects($this->once())
            ->method('fetchAll')
            ->with($this->_select, $bindParams)
            ->will($this->returnValue($this->_fixtureData))
        ;
        $this->_cache
            ->expects($this->once())
            ->method('save')
            ->with(serialize($this->_fixtureData), $cacheId, array('fixture_tag_one', 'fixture_tag_two'), 86400)
        ;
        $this->assertEquals($this->_fixtureData, $this->_object->fetchAll($this->_select, $bindParams));
    }
}
