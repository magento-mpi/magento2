<?php
/**
 * Unit test for Saas_Tenant_Loader
 */
class Saas_Tenant_LoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_mongoDb;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_collection;

    /**
     * @var Saas_Tenant_Loader
     */
    private $_loader;

    protected function setUp()
    {
        $this->_mongoDb = $this->getMock('MongoDB', array('selectCollection'), array(), '', false);
        $this->_collection = $this->getMock('MongoCollection', array('findOne'), array(), '', false);
        $this->_loader = new Saas_Tenant_Loader($this->_mongoDb);
    }

    public function testGetId()
    {
        $this->_mongoDb->expects($this->once())->method('selectCollection')
            ->with('tenantDomains')->will($this->returnValue($this->_collection));
        $this->_collection->expects($this->once())->method('findOne')
            ->with(array('domain' => 'example*com'))->will($this->returnValue(array('tenantId' => 256)));
        $this->assertEquals('256', $this->_loader->getId('example.com'));
        $this->assertEquals('256', $this->_loader->getId('example.com')); // repeat to ensure query is executed once
    }

    public function testGetIdWww()
    {
        $this->_mongoDb->expects($this->once())->method('selectCollection')
            ->with('tenantDomains')->will($this->returnValue($this->_collection));
        $this->_collection->expects($this->at(0))->method('findOne')->with(array('domain' => 'example*com'))
            ->will($this->returnValue(false));
        $this->_collection->expects($this->at(1))->method('findOne')->with(array('domain' => 'www*example*com'))
            ->will($this->returnValue(array('tenantId' => 454)));
        $this->assertEquals('454', $this->_loader->getId('www.example.com'));
    }

    public function testGetIdFalse()
    {
        $this->_mongoDb->expects($this->exactly(2))->method('selectCollection')
            ->with('tenantDomains')->will($this->returnValue($this->_collection));
        $this->_collection->expects($this->at(0))->method('findOne')->will($this->returnValue(array()));
        $this->_collection->expects($this->at(1))->method('findOne')->will($this->returnValue(array('tenantId' => 0)));
        $this->assertEquals(false, $this->_loader->getId('example.com'));
        $this->assertEquals(false, $this->_loader->getId('example2.com'));
    }

    /**
     * @param string $value
     * @dataProvider getIdExceptionDataProvider
     */
    public function testGetIdException($value)
    {
        $this->setExpectedException('InvalidArgumentException', 'Incorrect domain name "' . $value . '"');
        $this->_mongoDb->expects($this->any())->method('selectCollection') // just to prevent fatal error if test fails
            ->with('tenantDomains')->will($this->returnValue($this->_collection));
        $this->_loader->getId($value);
    }

    /**
     * @return array
     */
    public function getIdExceptionDataProvider()
    {
        return array(
            'empty value'                        => array(''),
            'missing TLD'                        => array('name'),
            'wrong TLD'                          => array('example.c'),
            'dot at the beginning'               => array('.example.com'),
            'too many dots'                      => array('test..example.com'),
            'label more than 63 chars'           => array(str_repeat('a', 100) . '.com'),
            'entire pattern more than 254 chars' => array(str_repeat('ab.', 99) . 'com'),
            // actually, this is a valid domain name, but not for our particular setup
            'fully qualified domain name'        => array('example.com.'),
        );
    }

    public function testGetData()
    {
        $this->_mongoDb->expects($this->exactly(2))->method('selectCollection')
            ->with('tenantConfiguration')->will($this->returnValue($this->_collection));
        $this->_collection->expects($this->at(0))->method('findOne')->with(array('tenantId' => 'one'))
            ->will($this->returnValue('data'));
        $this->_collection->expects($this->at(1))->method('findOne')->with(array('tenantId' => 'two'))
            ->will($this->returnValue(''));
        $this->assertEquals('data', $this->_loader->getData('one'));
        $this->assertFalse($this->_loader->getData('two'));
        $this->assertEquals('data', $this->_loader->getData('one')); // repeat to ensure resource is not invoked twice
    }
}
