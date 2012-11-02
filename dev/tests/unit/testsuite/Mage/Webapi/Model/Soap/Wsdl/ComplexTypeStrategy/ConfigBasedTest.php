<?php
/**
 * Complex type strategy tests.
 *
 * @copyright {}
 */
class Mage_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_ConfigBasedTest extends PHPUnit_Framework_TestCase
{
    public function testAddComplexType()
    {
        $this->markTestIncomplete('Incomplete test.');
        // TODO: finish test.
        $resourceConfigMock = $this->getMockBuilder('Mage_Webapi_Model_Config_Resource')
            ->setMethods(array('getDataType'))
            ->disableOriginalConstructor()
            ->getMock();
        $wsdlMock = $this->getMockBuilder('Mage_Webapi_Model_Soap_Wsdl')
            ->setMethods(array('toDomDocument'))
            ->disableOriginalConstructor()
            ->getMock();
        $domMock = $this->getMockBuilder('DOMDocument')
            ->disableOriginalConstructor()
            ->getMock();
        $wsdlMock->expects($this->once())
            ->method('toDomDocument')
            ->will($this->returnValue($domMock));

        $strategy = new Mage_Webapi_Model_Soap_Wsdl_ComplexTypeStrategy_ConfigBased($resourceConfigMock);
        $strategy->setContext($wsdlMock);

        $complexType = 'VendorModuleADataStructure';
        $complexTypeData = array(
            'documentation' => '',
            'parameters' => array(),
        );
        $resourceConfigMock->expects($this->any())
            ->method('getDataType')
            ->with($complexType)
            ->will($this->returnValue($complexTypeData));

        $strategy->addComplexType($complexType);
    }
}
