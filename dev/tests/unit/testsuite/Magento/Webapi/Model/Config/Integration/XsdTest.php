<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Config\Integration;

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
        $this->_schemaFile = BP . '/app/code/Magento/Webapi/etc/integration/api.xsd';
    }

    /**
     * @param string $fixtureXml
     * @param array $expectedErrors
     * @dataProvider exemplarXmlDataProvider
     */
    public function testExemplarXml($fixtureXml, array $expectedErrors)
    {
        $messageFormat = '%message%';
        $dom = new \Magento\Config\Dom($fixtureXml, array(), null, null, $messageFormat);
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
            'valid' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online" />
                        </resources>
                    </integration>
                </integrations>',
                array()
            ),
            'valid with several entities' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online" />
                        </resources>
                    </integration>
                    <integration name="TestIntegration2">
                        <resources>
                            <resource name="Magento_Catalog::product_read" />
                        </resources>
                    </integration>
                </integrations>',
                array()
            ),
            'empty root node' => array(
                '<integrations/>',
                array("Element 'integrations': Missing child element(s). Expected is ( integration ).")
            ),
            'empty integration' => array(
                '<integrations>
                    <integration name="TestIntegration" />
                </integrations>',
                array("Element 'integration': Missing child element(s). Expected is ( resources ).")
            ),
            'empty resources' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                        </resources>
                    </integration>
                </integrations>',
                array("Element 'resources': Missing child element(s). Expected is ( resource ).")
            ),
            'irrelevant root node' => array(
                '<integration name="TestIntegration"/>',
                array("Element 'integration': No matching global declaration available for the validation root.")
            ),
            'irrelevant node in root' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online" />
                        </resources>
                    </integration>
                    <invalid/>
                </integrations>',
                array("Element 'invalid': This element is not expected. Expected is ( integration ).")
            ),
            'irrelevant node in integration' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online" />
                        </resources>
                        <invalid/>
                    </integration>
                </integrations>',
                array("Element 'invalid': This element is not expected.")
            ),
            'irrelevant node in resources' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online" />
                        <invalid/>
                        </resources>
                    </integration>
                </integrations>',
                array("Element 'invalid': This element is not expected. Expected is ( resource ).")
            ),
            'irrelevant node in resource' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online">
                                <invalid/>
                            </resource>
                        </resources>
                    </integration>
                </integrations>',
                array(
                    "Element 'resource': Element content is not allowed, " .
                    "because the content type is a simple type definition."
                )
            ),
            'invalid attribute in root' => array(
                '<integrations invalid="invalid">
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online" />
                        </resources>
                    </integration>
                </integrations>',
                array("Element 'integrations', attribute 'invalid': The attribute 'invalid' is not allowed.")
            ),
            'invalid attribute in integration' => array(
                '<integrations>
                    <integration name="TestIntegration1" invalid="invalid">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online" />
                        </resources>
                    </integration>
                </integrations>',
                array("Element 'integration', attribute 'invalid': The attribute 'invalid' is not allowed.")
            ),
            'invalid attribute in resources' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources invalid="invalid">
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online" />
                        </resources>
                    </integration>
                </integrations>',
                array("Element 'resources', attribute 'invalid': The attribute 'invalid' is not allowed.")
            ),
            'invalid attribute in resource' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::manage" invalid="invalid" />
                            <resource name="Magento_Customer::online" />
                        </resources>
                    </integration>
                </integrations>',
                array("Element 'resource', attribute 'invalid': The attribute 'invalid' is not allowed.")
            ),
            'integration without name' => array(
                '<integrations>
                    <integration>
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online" />
                        </resources>
                    </integration>
                </integrations>',
                array("Element 'integration': The attribute 'name' is required but missing.")
            ),
            'integration with empty name' => array(
                '<integrations>
                    <integration name="">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="Magento_Customer::online" />
                        </resources>
                    </integration>
                </integrations>',
                array(
                    "Element 'integration', attribute 'name': [facet 'minLength'] The value '' has a length of '0'; " .
                    "this underruns the allowed minimum length of '2'.",
                    "Element 'integration', attribute 'name': " .
                    "'' is not a valid value of the atomic type 'integrationNameType'."
                )
            ),
            'resource without name' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource />
                        </resources>
                    </integration>
                </integrations>',
                array("Element 'resource': The attribute 'name' is required but missing.")
            ),
            'resource with empty name' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::manage" />
                            <resource name="" />
                        </resources>
                    </integration>
                </integrations>',
                array(
                    "Element 'resource', attribute 'name': [facet 'pattern'] " .
                    "The value '' is not accepted by the pattern '.+_.+::.+'.",
                    "Element 'resource', attribute 'name': '' " .
                    "is not a valid value of the atomic type 'resourceNameType'."
                )
            ),
            'resource with invalid name' => array(
                '<integrations>
                    <integration name="TestIntegration1">
                        <resources>
                            <resource name="Magento_Customer::online" />
                            <resource name="customer_manage" />
                        </resources>
                    </integration>
                </integrations>',
                array(
                    "Element 'resource', attribute 'name': [facet 'pattern'] " .
                    "The value 'customer_manage' is not accepted by the pattern '.+_.+::.+'.",
                    "Element 'resource', attribute 'name': 'customer_manage' " .
                    "is not a valid value of the atomic type 'resourceNameType'."
                )
            )
        );
    }
}
