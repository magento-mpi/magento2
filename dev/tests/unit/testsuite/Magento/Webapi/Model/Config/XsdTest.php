<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Config;

/**
 * Test for validation rules implemented by XSD schema for API integration configuration.
 */
class XsdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    protected function setUp()
    {
        $this->_schemaFile = BP . '/app/code/Magento/Webapi/etc/webapi.xsd';
    }

    /**
     * @param string $fixtureXml
     * @param array $expectedErrors
     * @dataProvider exemplarXmlDataProvider
     */
    public function testExemplarXml($fixtureXml, array $expectedErrors)
    {
        $messageFormat = '%message%';
        $dom = new \Magento\Config\Dom($fixtureXml, array(), null, $messageFormat);
        $actualResult = $dom->validate($this->_schemaFile, $actualErrors);
        $this->assertEquals(empty($expectedErrors), $actualResult, "Validation result is invalid.");
        $this->assertEquals($expectedErrors, $actualErrors, "Validation errors does not match.");
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function exemplarXmlDataProvider()
    {
        return array(
            /** Valid configurations */
            'valid' => array(
                '<config>
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV1Interface" baseUrl="/V1/testmodule1">
                        <rest-route httpMethod="GET" method="item" resources="Magento_TestModule1::resource1">/:id</rest-route>
                        <rest-route httpMethod="POST" method="create" isSecure="1" resources="Magento_TestModule1::resource1,Magento_TestModule1::resource2"></rest-route>
                    </service>
                </config>',
                array()
            ),
            'valid with several entities' => array(
                '<config>
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV1Interface" baseUrl="/V1/testmodule1">
                        <rest-route httpMethod="GET" method="item" resources="Magento_TestModule1::resource1">/:id</rest-route>
                        <rest-route httpMethod="GET" method="items" resources="Magento_TestModule1::resource2"></rest-route>
                        <rest-route httpMethod="POST" method="create" resources="Magento_TestModule1::resource3"></rest-route>
                        <rest-route httpMethod="PUT" method="update" resources="Magento_TestModule1::resource1,Magento_TestModule1::resource2">/:id</rest-route>
                    </service>

                    <service class="Magento\TestModule1\Service\AllSoapAndRestV2Interface" baseUrl="/V2/testmodule1">
                        <rest-route httpMethod="GET" method="item" resources="Magento_TestModule1::resource1">/:id</rest-route>
                        <rest-route httpMethod="GET" method="items" resources="Magento_TestModule1::resource2"></rest-route>
                        <rest-route httpMethod="POST" method="create" resources="Magento_TestModule1::resource3"></rest-route>
                        <rest-route httpMethod="PUT" method="update" resources="Magento_TestModule1::resource1,Magento_TestModule1::resource2">/:id</rest-route>
                        <rest-route httpMethod="DELETE" method="delete" resources="Magento_TestModule1::resource1">/:id</rest-route>
                    </service>

                    <service class="Magento\TestModule1\Service\AllSoapAndRestV2Interface" baseUrl="/V2/testmodule1">
                        <rest-route httpMethod="GET" method="item" resources="Magento_TestModule1::resource1">/:id</rest-route>
                        <rest-route httpMethod="GET" method="items" resources="Magento_TestModule1::resource2"></rest-route>
                        <rest-route httpMethod="POST" method="create" resources="Magento_TestModule1::resource3"></rest-route>
                        <rest-route httpMethod="PUT" method="update" resources="Magento_TestModule1::resource1,Magento_TestModule1::resource2">/:id</rest-route>
                        <rest-route httpMethod="DELETE" method="delete" resources="Magento_TestModule1::resource1">/:id</rest-route>
                    </service>
                </config>',
                array()
            ),

            /** Missing required nodes */
            'empty root node' => array(
                '<config/>',
                array("Element 'config': Missing child element(s). Expected is ( service ).")
            ),
            'empty rest-route' => array(
                '<config>
                    <service/>
                </config>',
                array("Element 'service': Missing child element(s). Expected is ( rest-route ).")
            ),
            'invalid rest-routes' => array(
                '<config>
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV2Interface">
                        <rest-route>
                            <invalid></invalid>
                        </rest-route>
                    </service>
                </config>',
                array("Element 'rest-route': Element content is not allowed, because the content type is a simple type definition.")
            ),
            'irrelevant root node' => array(
                '<invalid/>',
                array("Element 'invalid': No matching global declaration available for the validation root.")
            ),

            /** Excessive nodes */
            'irrelevant node in root' => array(
                '<config>
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV2Interface">
                        <rest-route>
                        </rest-route>
                    </service>
                    <invalid/>
                </config>',
                array("Element 'invalid': This element is not expected. Expected is ( service ).")
            ),
            'irrelevant node in service' => array(
                '<config>
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV2Interface">
                        <rest-route>
                        </rest-route>
                        <invalid/>
                    </service>
                </config>',
                array("Element 'invalid': This element is not expected. Expected is ( rest-route ).")
            ),
            'irrelevant node in rest-routes' => array(
                '<config>
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV2Interface">
                        <rest-route>
                            <invalid/>
                        </rest-route>
                    </service>
                </config>',
                array("Element 'rest-route': Element content is not allowed, because the content type is a simple type definition.")
            ),

            /** Excessive attributes */
            'invalid attribute in root' => array(
                '<config invalid="invalid">
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV2Interface" baseUrl="/V2/testmodule1">
                        <rest-route httpMethod="GET" method="item" resources="Magento_TestModule1::resource1">/:id</rest-route>
                    </service>
                </config>',
                array("Element 'config', attribute 'invalid': The attribute 'invalid' is not allowed.")
            ),
            'invalid attribute in service' => array(
                '<config>
                    <service invalid="invalid" class="Magento\TestModule1\Service\AllSoapAndRestV2Interface" baseUrl="/V2/testmodule1">
                        <rest-route httpMethod="GET" method="item" resources="Magento_TestModule1::resource1">/:id</rest-route>
                    </service>
                </config>',
                array("Element 'service', attribute 'invalid': The attribute 'invalid' is not allowed.")
            ),
            'invalid attribute in rest-routes' => array(
                '<config>
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV2Interface" baseUrl="/V2/testmodule1">
                        <rest-route invalid="invalid" httpMethod="GET" method="item" resources="Magento_TestModule1::resource1">/:id</rest-route>
                    </service>
                </config>',
                array("Element 'rest-route', attribute 'invalid': The attribute 'invalid' is not allowed.")
            ),

            /** Invalid values */
            'rest-route with invalid httpMethod' => array(
                '<config>
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV1Interface" baseUrl="/V1/testmodule1">
                        <rest-route httpMethod="INVALID" method="item" resources="Magento_TestModule1::resource1">/:id</rest-route>
                    </service>
                </config>',
                array(
                    "Element 'rest-route', attribute 'httpMethod': [facet 'enumeration'] "
                    . "The value 'INVALID' is not an element of the set {'GET', 'PUT', 'POST', 'DELETE'}.",
                    "Element 'rest-route', attribute 'httpMethod': 'INVALID' is not a valid value of the local atomic type.")
            ),
            'rest-route with invalid isSecure key type' => array(
                '<config>
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV1Interface" baseUrl="/V1/testmodule1">
                        <rest-route isSecure="Invalid"></rest-route>
                    </service>
                </config>',
                array(
                    "Element 'rest-route', attribute 'isSecure': "
                    . "'Invalid' is not a valid value of the atomic type 'xs:boolean'.")
            ),
            'rest-route with invalid resources type' => array(
                '<config>
                    <service class="Magento\TestModule1\Service\AllSoapAndRestV1Interface" baseUrl="/V1/testmodule1">
                        <rest-route resources="Invalid"></rest-route>
                    </service>
                </config>',
                array(
                    "Element 'rest-route', attribute 'resources': [facet 'pattern'] "
                    . "The value 'Invalid' is not accepted by the pattern '.+::.+(, ?.+::.+)*'.",
                "Element 'rest-route', attribute 'resources': 'Invalid' is not a valid value of the atomic type 'resourcesType'."
                )
            )
        );
    }
}
