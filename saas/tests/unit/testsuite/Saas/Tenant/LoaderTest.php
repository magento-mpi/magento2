<?php
/**
 * Unit test for Saas_Tenant_Loader
 */
class Saas_Tenant_LoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $mongoDb;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $collection;

    /**
     * @var Saas_Tenant_Loader
     */
    private $loader;

    protected function setUp()
    {
        $this->mongoDb = $this->getMock('MongoDB', array('selectCollection'), array(), '', false);
        $this->collection = $this->getMock('MongoCollection', array('findOne'), array(), '', false);
        $this->loader = new Saas_Tenant_Loader($this->mongoDb);
    }

    public function testGetId()
    {
        $this->mongoDb->expects($this->once())->method('selectCollection')
            ->with('tenantDomains')->will($this->returnValue($this->collection));
        $this->collection->expects($this->once())->method('findOne')
            ->with(array('domain' => 'example*com'))->will($this->returnValue(array('tenantId' => 256)));
        $this->assertEquals('256', $this->loader->getId('example.com'));
        $this->assertEquals('256', $this->loader->getId('example.com')); // repeat to ensure query is executed once
    }

    public function testGetIdWww()
    {
        $this->mongoDb->expects($this->once())->method('selectCollection')
            ->with('tenantDomains')->will($this->returnValue($this->collection));
        $this->collection->expects($this->at(0))->method('findOne')->with(array('domain' => 'example*com'))
            ->will($this->returnValue(false));
        $this->collection->expects($this->at(1))->method('findOne')->with(array('domain' => 'www*example*com'))
            ->will($this->returnValue(array('tenantId' => 454)));
        $this->assertEquals('454', $this->loader->getId('www.example.com'));
    }

    public function testGetIdFalse()
    {
        $this->mongoDb->expects($this->exactly(2))->method('selectCollection')
            ->with('tenantDomains')->will($this->returnValue($this->collection));
        $this->collection->expects($this->at(0))->method('findOne')->will($this->returnValue(array()));
        $this->collection->expects($this->at(1))->method('findOne')->will($this->returnValue(array('tenantId' => 0)));
        $this->assertEquals(false, $this->loader->getId('example.com'));
        $this->assertEquals(false, $this->loader->getId('example2.com'));
    }

    /**
     * @param string $value
     * @dataProvider getIdExceptionDataProvider
     */
    public function testGetIdException($value)
    {
        $this->setExpectedException('InvalidArgumentException', 'Incorrect domain name "' . $value . '"');
        $this->mongoDb->expects($this->any())->method('selectCollection') // just to prevent fatal error if test fails
            ->with('tenantDomains')->will($this->returnValue($this->collection));
        $this->loader->getId($value);
    }

    /**
     * @return array
     */
    public function getIdExceptionDataProvider()
    {
        return array(
            'empty value' => array(''),
            'missing TLD' => array('name'),
            'wrong TLD' => array('example.c'),
            'dot at the beginning' => array('.example.com'),
            'too many dots' => array('test..example.com'),
            'label more than 63 chars' => array(implode('', array_pad(array(), 100, 'a')) . '.com'),
            'entire pattern more than 254 chars' => array(implode('', array_pad(array(), 99, 'ab.')) . 'com'),
            // actually, this is a valid domain name, but not for our particular setup
            'fully qualified domain name' => array('example.com.'),
        );
    }

    public function testGetData()
    {
        $this->mongoDb->expects($this->exactly(2))->method('selectCollection')
            ->with('tenantConfiguration')->will($this->returnValue($this->collection));
        $this->collection->expects($this->at(0))->method('findOne')->with(array('tenantId' => 'one'))
            ->will($this->returnValue('data'));
        $this->collection->expects($this->at(1))->method('findOne')->with(array('tenantId' => 'two'))
            ->will($this->returnValue(''));
        $this->assertEquals('data', $this->loader->getData('one'));
        $this->assertFalse($this->loader->getData('two'));
        $this->assertEquals('data', $this->loader->getData('one')); // repeat to ensure resource is not invoked twice
    }
}
