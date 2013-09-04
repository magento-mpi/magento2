<?php
/**
 * Test format of layout files
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Layout_HandlesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test dependencies between handle attributes that is out of coverage by XSD
     *
     * @param string $layoutFile
     * @dataProvider layoutFilesDataProvider
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function testHandleDeclaration($layoutFile)
    {
        $issues = array();
        $node = simplexml_load_file($layoutFile);
        $type = $node['type'];
        $parent = $node['parent'];
        $owner = $node['owner'];
        $label = $node['label'];
        if ($type) {
            switch ($type) {
                case 'page':
                    if ($owner) {
                        $issues[] = 'Attribute "owner" is inappropriate for page types';
                    }
                    break;
                case 'fragment':
                    if ($parent) {
                        $issues[] = 'Attribute "parent" is inappropriate for page fragment types';
                    }
                    if (!$owner) {
                        $issues[] = 'No attribute "owner" is specified for page fragment type';
                    }
                    break;
            }
        } else {
            if ($label) {
                $issues[] = 'Attribute "label" is defined, but "type" is not';
            }
            if ($parent || $owner) {
                $issues[] = 'Attribute "parent" and/or "owner" is defined, but "type" is not';
            }
        }
        if ($issues) {
            $this->fail("Issues found in handle declaration:\n" . implode("\n", $issues) . "\n");
        }
    }

    /**
     * Test dependencies between container attributes that is out of coverage by XSD
     *
     * @param string $layoutFile
     * @dataProvider layoutFilesDataProvider
     */
    public function testContainerDeclaration($layoutFile)
    {
        $issues = array();
        $xml = simplexml_load_file($layoutFile);
        $containers = $xml->xpath('/layout//container') ?: array();
        /** @var SimpleXMLElement $node */
        foreach ($containers as $node) {
            if (!isset($node['htmlTag']) && (isset($node['htmlId']) || isset($node['htmlClass']))) {
                $issues[] = $node->asXML();
            }
        }
        if ($issues) {
            $message = 'The following containers declare attribute "htmlId" and/or "htmlClass", but not "htmlTag":';
            $this->fail($message . "\n" . implode("\n", $issues) . "\n");
        }
    }

    /**
     * Test format of a layout file using XSD
     *
     * @param string $layoutFile
     * @dataProvider layoutFilesDataProvider
     */
    public function testLayoutFormat($layoutFile)
    {
        $schemaFile = __DIR__ . '/../../../../../../app/code/Mage/Core/etc/layouts.xsd';
        $domLayout = new Magento_Config_Dom(file_get_contents($layoutFile));
        $result = $domLayout->validate($schemaFile, $errors);
        $this->assertTrue($result, print_r($errors, true));
    }

    /**
     * @return array
     */
    public function layoutFilesDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getLayoutFiles();
    }
}
