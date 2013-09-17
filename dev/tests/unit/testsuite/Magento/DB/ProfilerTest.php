<?php
/**
 * Magento_DB_Profiler test case
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_DB_ProfilerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Profiler instance for test
     * @var Magento_DB_Profiler
     */
    protected $_profiler;

    /**
     * Setup
     */
    protected function setUp()
    {
        $this->_profiler = new Magento_DB_Profiler(true);
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
        $this->assertEquals(Magento_DB_Profiler::STORED, $endResult);
    }

    public function testQueryEndLast()
    {
        $this->_profiler->queryStart('SELECT * FROM table');
        $endResult = $this->_profiler->queryEndLast();
        $this->assertAttributeEquals(null, '_lastQueryId', $this->_profiler);
        $this->assertEquals(Magento_DB_Profiler::STORED, $endResult);

        $endResult = $this->_profiler->queryEndLast();
        $this->assertEquals(Magento_DB_Profiler::IGNORED, $endResult);
    }
}
