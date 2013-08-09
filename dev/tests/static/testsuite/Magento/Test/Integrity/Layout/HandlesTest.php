<?php
/**
 * Test declarations of handles in layouts
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_Integrity_Layout_HandleTest extends PHPUnit_Framework_TestCase
{
    /**
     * Pattern for attribute elements, compatible with HTML ID
     */
    const HTML_ID_PATTERN = '/^[a-z][a-z\ \-\_\d]*$/';

    /**
     * Suppressing PHPMD because of complex logic of validation. The problem is architectural, rather than in the test
     *
     * @param string $file
     * @dataProvider layoutFilesDataProvider
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function testHandleDeclaration($file)
    {
        $issues = array();
        $xml = simplexml_load_file($file);
        $handles = $xml->xpath('/layout/*');
        /** @var $node SimpleXMLElement */
        foreach ($handles as $node) {
            $handleMessage = "Handle '{$node->getName()}':";
            $type = $node['type'];
            $parent = $node['parent'];
            $owner = $node['owner'];
            if ($type) {
                switch ($type) {
                    case 'page':
                        if ($owner) {
                            $issues[] = "{$handleMessage} attribute 'owner' is inappropriate for page types";
                        }
                        break;
                    case 'fragment':
                        if ($parent) {
                            $issues[] = "{$handleMessage} attribute 'parent' is inappropriate for page fragment types";
                        }
                        if (!$owner) {
                            $issues[] = "{$handleMessage} no 'owner' specified for page fragment type";
                        }
                        break;
                    default:
                        $issues[] = "{$handleMessage} unknown type '{$type}'";
                        break;
                }
            } else {
                if ($node->xpath('child::label')) {
                    $issues[] = "{$handleMessage} 'label' child node is defined, but 'type' attribute is not";
                }
                if ($parent || $owner) {
                    $issues[] = "{$handleMessage} 'parent' or 'owner' is defined, but 'type' is not";
                }
            }
        }
        if (!empty($issues)) {
            $this->fail(sprintf("Issues found in handle declaration:\n%s\n", implode("\n", $issues)));
        }
    }

    /**
     * Suppressing PHPMD issues because this test is complex and it is not reasonable to separate it
     *
     * @param string $file
     * @dataProvider layoutFilesDataProvider
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function testContainerDeclaration($file)
    {
        $xml = simplexml_load_file($file);
        $containers = $xml->xpath('/layout//container') ?: array();
        $errors = array();
        /** @var SimpleXMLElement $node */
        foreach ($containers as $node) {
            $nodeErrors = array();
            $attr = $node->attributes();
            if (!isset($attr['name'])) {
                $nodeErrors[] = '"name" attribute is not specified';
            } elseif (!preg_match('/^[a-z][a-z\-\_\d\.]*$/i', $attr['name'])) {
                $nodeErrors[] = 'specified value for "name" attribute is invalid';
            }
            if (!isset($attr['label']) || '' == $attr['label']) {
                $nodeErrors[] = '"label" attribute is not specified or empty';
            }
            if (isset($attr['as']) && !preg_match('/^[a-z\d\-\_]+$/i', $attr['as'])) {
                $nodeErrors[] = 'specified value for "as" attribute is invalid';
            }
            if (isset($attr['htmlTag']) && !preg_match('/^[a-z]+$/', $attr['htmlTag'])) {
                $nodeErrors[] = 'specified value for "htmlTag" attribute is invalid';
            }
            if (!isset($attr['htmlTag']) && (isset($attr['htmlId']) || isset($attr['htmlClass']))) {
                $nodeErrors[] = 'having "htmlId" or "htmlClass" attributes don\'t make sense without "htmlTag"';
            }
            if (isset($attr['htmlId']) && !preg_match(self::HTML_ID_PATTERN, $attr['htmlId'])) {
                $nodeErrors[] = 'specified value for "htmlId" attribute is invalid';
            }
            if (isset($attr['htmlClass']) && !preg_match(self::HTML_ID_PATTERN, $attr['htmlClass'])) {
                $nodeErrors[] = 'specified value for "htmlClass" attribute is invalid';
            }
            $allowedAttributes = array(
                'name', 'label', 'as', 'htmlTag', 'htmlId', 'htmlClass', 'output', 'before', 'after'
            );
            foreach ($attr as $key => $attribute) {
                if (!in_array($key, $allowedAttributes)) {
                    $nodeErrors[] = 'unexpected attribute "' . $key . '"';
                }
            }
            if ($nodeErrors) {
                $errors[] = "\n" . $node->asXML() . "\n - " . implode("\n - ", $nodeErrors);
            }
        }
        if ($errors) {
            $this->fail(implode("\n\n", $errors));
        }
    }

    /**
     * @param string $file
     * @dataProvider layoutFilesDataProvider
     */
    public function testHandlesConvention($file)
    {
        // One handle per file
        $xml = simplexml_load_file($file);
        $handles = $xml->children();
        $this->assertEquals(1, count($handles), 'There should be exactly 1 handle declared per layout file');

        // Name of file is same as handle name
        $skippedPrefix = 'install_wizard_config_'; // Several files, that do not follow the convention
        $basename = basename($file);
        if (substr($basename, 0, strlen($skippedPrefix)) != $skippedPrefix) {
            $handle = $handles[0];
            $this->assertEquals($handle->getName() . ".xml", $basename);
        }
    }

    /**
     * @return array
     */
    public function layoutFilesDataProvider()
    {
        return Magento_TestFramework_Utility_Files::init()->getLayoutFiles();
    }
}
