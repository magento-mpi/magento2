<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Config;

/**
 * Test for class reflector.
 */
class ClassReflectorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Reflection\TypeProcessor|\PHPUnit_Framework_MockObject_MockObject */
    protected $_typeProcessor;

    /** @var \Magento\Webapi\Model\Config\ClassReflector */
    protected $_classReflector;

    /**
     * Set up helper.
     */
    protected function setUp()
    {
        $this->_typeProcessor = $this->getMock(
            '\Magento\Framework\Reflection\TypeProcessor',
            array('process'),
            array(),
            '',
            false
        );
        $this->_typeProcessor->expects(
            $this->any()
        )->method(
            'process'
        )->will(
            $this->returnValueMap(array(array('string', 'str'), array('int', 'int')))
        );
        $this->_classReflector = new \Magento\Webapi\Model\Config\ClassReflector($this->_typeProcessor);
    }

    public function testReflectClassMethods()
    {
        $data = $this->_classReflector->reflectClassMethods(
            '\\Magento\\Webapi\\Model\\Config\\TestServiceForClassReflector',
            array('generateRandomString' => array('method' => 'generateRandomString'))
        );
        $this->assertEquals(array('generateRandomString' => $this->_getSampleReflectionData()), $data);
    }

    public function testExtractMethodData()
    {
        $classReflection = new \Zend\Server\Reflection\ReflectionClass(
            new \ReflectionClass('\\Magento\\Webapi\\Model\\Config\\TestServiceForClassReflector')
        );
        /** @var $methodReflection ReflectionMethod */
        $methodReflection = $classReflection->getMethods()[0];
        $methodData = $this->_classReflector->extractMethodData($methodReflection);
        $expectedResponse = $this->_getSampleReflectionData();
        $this->assertEquals($expectedResponse, $methodData);
    }

    /**
     * Expected reflection data for TestServiceForClassReflector generateRandomString method
     *
     * @return array
     */
    protected function _getSampleReflectionData()
    {
        return array(
            'documentation' => 'Basic random string generator',
            'interface' => array(
                'in' => array(
                    'parameters' => array(
                        'length' => array(
                            'type' => 'int',
                            'required' => true,
                            'documentation' => 'length of the random string'
                        )
                    )
                ),
                'out' => array(
                    'parameters' => array(
                        'result' => array('type' => 'str', 'documentation' => 'random string', 'required' => true)
                    )
                )
            )
        );
    }
}
