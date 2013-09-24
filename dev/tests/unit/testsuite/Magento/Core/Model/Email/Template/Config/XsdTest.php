<?php
/**
 * Test for validation rules implemented by XSD schemas for email templates configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Email_Template_Config_XsdTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test validation rules implemented by XSD schema for individual config files
     *
     * @param string $fixtureXml
     * @param array $expectedErrors
     * @dataProvider individualXmlDataProvider
     */
    public function testIndividualXml($fixtureXml, array $expectedErrors)
    {
        $schemaFile = BP . '/app/code/Magento/Core/etc/email_templates_file.xsd';
        $this->_testXmlAgainstXsd($fixtureXml, $schemaFile, $expectedErrors);
    }

    public function individualXmlDataProvider()
    {
        return array(
            'valid' => array(
                '<config><template id="test" label="Test" file="test.html" type="html"/></config>',
                array()
            ),
            'empty root node' => array(
                '<config/>',
                array("Element 'config': Missing child element(s). Expected is ( template ).")
            ),
            'irrelevant root node' => array(
                '<template id="test" label="Test" file="test.html" type="html"/>',
                array("Element 'template': No matching global declaration available for the validation root.")
            ),
            'invalid node' => array(
                '<config><invalid/></config>',
                array("Element 'invalid': This element is not expected. Expected is ( template ).")
            ),
            'node "template" with value' => array(
                '<config><template id="test" label="Test" file="test.html" type="html">invalid</template></config>',
                array("Element 'template': Character content is not allowed, because the content type is empty.")
            ),
            'node "template" with children' => array(
                '<config><template id="test" label="Test" file="test.html" type="html"><invalid/></template></config>',
                array("Element 'template': Element content is not allowed, because the content type is empty.")
            ),
            'node "template" without attribute "id"' => array(
                '<config><template label="Test" file="test.html" type="html"/></config>',
                array("Element 'template': The attribute 'id' is required but missing.")
            ),
            'node "template" without attribute "label"' => array(
                '<config><template id="test" file="test.html" type="html"/></config>',
                array("Element 'template': The attribute 'label' is required but missing.")
            ),
            'node "template" without attribute "file"' => array(
                '<config><template id="test" label="Test" type="html"/></config>',
                array("Element 'template': The attribute 'file' is required but missing.")
            ),
            'node "template" without attribute "type"' => array(
                '<config><template id="test" label="Test" file="test.html"/></config>',
                array("Element 'template': The attribute 'type' is required but missing.")
            ),
            'node "template" with invalid attribute "type"' => array(
                '<config><template id="test" label="Test" file="test.html" type="invalid"/></config>',
                array(
                    "Element 'template', attribute 'type': "
                        . "[facet 'enumeration'] The value 'invalid' is not an element of the set {'html', 'text'}.",
                    "Element 'template', attribute 'type': "
                        . "'invalid' is not a valid value of the atomic type 'emailTemplateFormatType'.",
                )
            ),
            'node "template" with unknown attribute "module"' => array(
                '<config><template id="test" label="Test" file="test.html" type="html" module="Test_Module"/></config>',
                array("Element 'template', attribute 'module': The attribute 'module' is not allowed.")
            ),
        );
    }

    /**
     * Test validation rules implemented by XSD schema for merged configs
     *
     * @param string $fixtureXml
     * @param array $expectedErrors
     * @dataProvider mergedXmlDataProvider
     */
    public function testMergedXml($fixtureXml, array $expectedErrors)
    {
        $schemaFile = BP . '/app/code/Magento/Core/etc/email_templates.xsd';
        $this->_testXmlAgainstXsd($fixtureXml, $schemaFile, $expectedErrors);
    }

    public function mergedXmlDataProvider()
    {
        return array(
            'valid' => array(
                '<config><template id="test" label="Test" file="test.txt" type="text" module="Module"/></config>',
                array()
            ),
            'empty root node' => array(
                '<config/>',
                array("Element 'config': Missing child element(s). Expected is ( template ).")
            ),
            'irrelevant root node' => array(
                '<template id="test" label="Test" file="test.txt" type="text" module="Module"/>',
                array("Element 'template': No matching global declaration available for the validation root.")
            ),
            'invalid node' => array(
                '<config><invalid/></config>',
                array("Element 'invalid': This element is not expected. Expected is ( template ).")
            ),
            'node "template" with value' => array(
                '<config>
                    <template id="test" label="Test" file="test.txt" type="text" module="Module">invalid</template>
                </config>',
                array("Element 'template': Character content is not allowed, because the content type is empty.")
            ),
            'node "template" with children' => array(
                '<config>
                    <template id="test" label="Test" file="test.txt" type="text" module="Module"><invalid/></template>
                </config>',
                array("Element 'template': Element content is not allowed, because the content type is empty.")
            ),
            'node "template" without attribute "id"' => array(
                '<config><template label="Test" file="test.txt" type="text" module="Module"/></config>',
                array("Element 'template': The attribute 'id' is required but missing.")
            ),
            'node "template" without attribute "label"' => array(
                '<config><template id="test" file="test.txt" type="text" module="Module"/></config>',
                array("Element 'template': The attribute 'label' is required but missing.")
            ),
            'node "template" without attribute "file"' => array(
                '<config><template id="test" label="Test" type="text" module="Module"/></config>',
                array("Element 'template': The attribute 'file' is required but missing.")
            ),
            'node "template" without attribute "type"' => array(
                '<config><template id="test" label="Test" file="test.txt" module="Module"/></config>',
                array("Element 'template': The attribute 'type' is required but missing.")
            ),
            'node "template" with invalid attribute "type"' => array(
                '<config><template id="test" label="Test" file="test.txt" type="invalid" module="Module"/></config>',
                array(
                    "Element 'template', attribute 'type': "
                    . "[facet 'enumeration'] The value 'invalid' is not an element of the set {'html', 'text'}.",
                    "Element 'template', attribute 'type': "
                    . "'invalid' is not a valid value of the atomic type 'emailTemplateFormatType'.",
                )
            ),
            'node "template" without attribute "module"' => array(
                '<config><template id="test" label="Test" file="test.txt" type="text"/></config>',
                array("Element 'template': The attribute 'module' is required but missing.")
            ),
            'node "template" with unknown attribute' => array(
                '<config>
                    <template id="test" label="Test" file="test.txt" type="text" module="Module" unknown="true"/>
                </config>',
                array("Element 'template', attribute 'unknown': The attribute 'unknown' is not allowed.")
            ),
        );
    }

    /**
     * Test that XSD schema validates fixture XML contents producing expected results
     *
     * @param string $fixtureXml
     * @param string $schemaFile
     * @param array $expectedErrors
     */
    protected function _testXmlAgainstXsd($fixtureXml, $schemaFile, array $expectedErrors)
    {
        $dom = new Magento_Config_Dom($fixtureXml, array(), null, '%message%');
        $actualResult = $dom->validate($schemaFile, $actualErrors);
        $this->assertEquals(empty($expectedErrors), $actualResult);
        $this->assertEquals($expectedErrors, $actualErrors);
    }
}
