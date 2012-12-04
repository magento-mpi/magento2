<?php
/**
 * Varien_Db_Profiler test case
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Varien_Db_ProfilerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Profiler instance for test
     * @var Varien_Db_Profiler
     */
    protected $_profiler;

    /**
     * Setup
     */
    protected function setUp()
    {
        $this->_profiler = new Varien_Db_Profiler(true);
    }

    public function testSetHost()
    {
        $this->_profiler->setHost('localhost');
        $this->assertAttributeEquals('localhost', '_host', $this->_profiler);
    }

    public function testSetType()
    {
        $this->_profiler->setType('mysql');
        $this->assertAttributeEquals('mysql', '_type', $this->_profiler);
    }

    public function testQueryStart()
    {
        $lastQueryId = $this->_profiler->queryStart('SELECT * FROM table');
        $this->assertEquals(null, $lastQueryId);
    }

    public function testQueryEnd()
    {
        $lastQueryId = $this->_profiler->queryStart('SELECT * FROM table');
        $endResult = $this->_profiler->queryEnd($lastQueryId);
        $this->assertAttributeEquals(null, '_lastQueryId', $this->_profiler);
        $this->assertEquals(Zend_Db_Profiler::STORED, $endResult);
    }

    public function testQueryEndLast()
    {
        $this->_profiler->queryStart('SELECT * FROM table');
        $endResult = $this->_profiler->queryEndLast();
        $this->assertAttributeEquals(null, '_lastQueryId', $this->_profiler);
        $this->assertEquals(Zend_Db_Profiler::STORED, $endResult);

        $endResult = $this->_profiler->queryEndLast();
        $this->assertEquals(Zend_Db_Profiler::IGNORED, $endResult);
    }
}
