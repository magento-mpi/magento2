<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Soap\Wsdl\ComplexTypeStrategy;

use Zend\Soap\Wsdl;

/**
 * Complex type strategy tests.
 */
class ConfigBasedTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor|\PHPUnit_Framework_MockObject_MockObject */
    protected $_typeProcessor;

    /** @var \Magento\Webapi\Model\Soap\Wsdl|\PHPUnit_Framework_MockObject_MockObject */
    protected $_wsdl;

    /** @var ConfigBased */
    protected $_strategy;

    /**
     * Set up strategy for test.
     */
    protected function setUp()
    {
        $this->_typeProcessor = $this->getMockBuilder('Magento\Webapi\Model\Soap\Config\Reader\TypeProcessor')
            ->setMethods(array('getTypeData'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_wsdl = $this->getMockBuilder('Magento\Webapi\Model\Soap\Wsdl')
            ->setMethods(array('toDomDocument', 'getTypes', 'getSchema'))
            ->disableOriginalConstructor()
            ->getMock();
        $this->_strategy = new ConfigBased($this->_typeProcessor);
        $this->_strategy->setContext($this->_wsdl);
        parent::setUp();
    }

    /**
     * Clean up.
     */
    protected function tearDown()
    {
        unset($this->_typeProcessor);
        unset($this->_strategy);
        unset($this->_wsdl);

        parent::tearDown();
    }

    /**
     * Test that addComplexType returns type WSDL name
     * if it has already been processed (registered at includedTypes in WSDL)
     */
    public function testCheckTypeName()
    {
        $testType = 'testComplexTypeName';
        $testTypeWsdlName = 'tns:' . $testType;
        $includedTypes = array(
            $testType => $testTypeWsdlName,
        );
        $this->_wsdl->expects($this->exactly(2))
            ->method('getTypes')
            ->will($this->returnValue($includedTypes));

        $this->assertEquals($testTypeWsdlName, $this->_strategy->addComplexType($testType));
    }

    /**
     * Test adding complex type with simple parameters.
     *
     * @param string $type
     * @param array $data
     * @dataProvider addComplexTypeDataProvider
     */
    public function testAddComplexTypeSimpleParameters($type, $data)
    {
        $this->_wsdl->expects($this->any())
            ->method('getTypes')
            ->will($this->returnValue(array()));

        $this->_wsdl->expects($this->any())
            ->method('toDomDocument')
            ->will($this->returnValue(new \DOMDocument()));

        $schemaMock = $this->_getDomElementMock();
        $schemaMock->expects($this->any())
            ->method('appendChild');
        $this->_wsdl->expects($this->any())
            ->method('getSchema')
            ->will($this->returnValue($schemaMock));

        $this->_typeProcessor->expects($this->at(0))
            ->method('getTypeData')
            ->with($type)
            ->will($this->returnValue($data));

        $this->assertEquals(Wsdl::TYPES_NS . ':' . $type, $this->_strategy->addComplexType($type));
    }

    /**
     * Data provider for testAddComplexTypeSimpleParameters().
     *
     * @return array
     */
    public static function addComplexTypeDataProvider()
    {
        return array(
            'simple parameters' => array(
                'VendorModuleADataStructure',
                array(
                    'documentation' => 'test',
                    'parameters' => array(
                        'string_param' => array(
                            'type' => 'string',
                            'required' => true,
                            'documentation' => 'Required string param.'
                        ),
                        'int_param' => array(
                            'type' => 'int',
                            'required' => true,
                            'documentation' => 'Required int param.'
                        ),
                        'bool_param' => array(
                            'type' => 'boolean',
                            'required' => false,
                            'documentation' => 'Optional complex type param.{annotation:test}'
                        ),
                    ),
                ),
            ),
            'type with call info' => array(
                'VendorModuleADataStructure',
                array(
                    'documentation' => 'test',
                    'parameters' => array(
                        'string_param' => array(
                            'type' => 'string',
                            'required' => false,
                            'documentation' => '{callInfo:VendorModuleACreate:requiredInput:conditionally}',
                        ),
                    ),
                    'callInfo' => array(
                        'requiredInput' => array(
                            'yes' => array(
                                'calls' => array('VendorModuleACreate')
                            )
                        ),
                        'returned' => array(
                            'always' => array(
                                'calls' => array('VendorModuleAGet')
                            )
                        )
                    ),
                ),
            ),
            'parameter with call info' => array(
                'VendorModuleADataStructure',
                array(
                    'documentation' => 'test',
                    'parameters' => array(
                        'string_param' => array(
                            'type' => 'string',
                            'required' => false,
                            'documentation' => '{callInfo:VendorModuleACreate:requiredInput:conditionally}'
                                . '{callInfo:allCallsExcept(VendorModuleAGet):returned:always}',
                        ),
                    ),
                ),
            ),
            'parameter with see link' => array(
                'VendorModuleADataStructure',
                array(
                    'documentation' => 'test',
                    'parameters' => array(
                        'string_param' => array(
                            'type' => 'string',
                            'required' => false,
                            'documentation' => '{seeLink:http://google.com/:title:for}',
                        ),
                    ),
                ),
            ),
            'parameter with doc instructions' => array(
                'VendorModuleADataStructure',
                array(
                    'documentation' => 'test',
                    'parameters' => array(
                        'string_param' => array(
                            'type' => 'string',
                            'required' => false,
                            'documentation' => '{docInstructions:output:noDoc}',
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Test adding complex type with complex parameters and arrays.
     */
    public function testAddComplexTypeComplexParameters()
    {
        $type = 'VendorModuleADataStructure';
        $parameterType = 'ComplexType';
        $typeData = array(
            'documentation' => 'test',
            'parameters' => array(
                'complex_param' => array(
                    'type' => $parameterType,
                    'required' => true,
                    'documentation' => 'complex type param.'
                ),
            ),
        );
        $parameterData = array(
            'documentation' => 'test',
            'parameters' => array(
                'string_param' => array(
                    'type' => 'ComplexTypeB[]',
                    'required' => true,
                    'documentation' => 'string param.'
                ),
            ),
        );

        $this->_wsdl->expects($this->at(0))
            ->method('getTypes')
            ->will($this->returnValue(array()));
        $this->_wsdl->expects($this->any())
            ->method('getTypes')
            ->will($this->returnValue(array($type => Wsdl::TYPES_NS . ':' . $type)));

        $this->_wsdl->expects($this->any())
            ->method('toDomDocument')
            ->will($this->returnValue(new \DOMDocument()));
        $schemaMock = $this->_getDomElementMock();
        $schemaMock->expects($this->any())
            ->method('appendChild');
        $this->_wsdl->expects($this->any())
            ->method('getSchema')
            ->will($this->returnValue($schemaMock));
        $this->_typeProcessor->expects($this->at(0))
            ->method('getTypeData')
            ->with($type)
            ->will($this->returnValue($typeData));
        $this->_typeProcessor->expects($this->at(1))
            ->method('getTypeData')
            ->with($parameterType)
            ->will($this->returnValue($parameterData));

        $this->assertEquals(Wsdl::TYPES_NS . ':' . $type, $this->_strategy->addComplexType($type));
    }

    /**
     * Create mock for DOMElement.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getDomElementMock()
    {
        return $this->getMockBuilder('DOMElement')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
