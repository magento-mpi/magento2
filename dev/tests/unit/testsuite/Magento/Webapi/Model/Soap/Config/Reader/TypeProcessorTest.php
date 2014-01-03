<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Soap\Config\Reader;

use Zend\Code\Reflection\ClassReflection;

/**
 * Type processor Test
 */
class TypeProcessorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor */
    protected $_typeProcessor;

    /** @var \Magento\Webapi\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $_helperMock;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $this->_helperMock = $this->getMock('Magento\Webapi\Helper\Data', [], [], '', false);
        $this->_typeProcessor = new \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor($this->_helperMock);
    }

    /**
     * Test Retrieving of processed types data.
     */
    public function testGetTypesData()
    {
        $this->_typeProcessor->setTypeData('typeA', ['dataA']);
        $this->_typeProcessor->setTypeData('typeB', ['dataB']);
        $this->assertEquals(['typeA' => ['dataA'], 'typeB' => ['dataB']], $this->_typeProcessor->getTypesData());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Data type "NonExistentType" is not declared.
     */
    public function testGetTypeDataInvalidArgumentException()
    {
        $this->_typeProcessor->getTypeData('NonExistentType');
    }

    /**
     * Test retrieval of data type details for the given type name.
     */
    public function testGetTypeData()
    {
        $this->_typeProcessor->setTypeData('typeA', ['dataA']);
        $this->assertEquals(['dataA'], $this->_typeProcessor->getTypeData('typeA'));
    }

    /**
     * Test data type details for the same type name set multiple times.
     */
    public function testSetTypeDataArrayMerge()
    {
        $this->_typeProcessor->setTypeData('typeA', ['dataA1']);
        $this->_typeProcessor->setTypeData('typeA', ['dataA2']);
        $this->_typeProcessor->setTypeData('typeA', ['dataA3']);
        $this->assertEquals(['dataA1', 'dataA2', 'dataA3'], $this->_typeProcessor->getTypeData('typeA'));
    }
}
