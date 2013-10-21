<?php
/**
 * Test format of layout files
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Layout;

class HandlesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function testHandleDeclarations()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * Test dependencies between handle attributes that is out of coverage by XSD
             *
             * @param string $layoutFile
             */
            function ($layoutFile) {
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
            },
            \Magento\TestFramework\Utility\Files::init()->getLayoutFiles()
        );
    }

    public function testContainerDeclarations()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * Test dependencies between container attributes that is out of coverage by XSD
             *
             * @param string $layoutFile
             */
            function ($layoutFile) {
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
                    $this->fail(
                        'The following containers declare attribute "htmlId" and/or "htmlClass", but not "htmlTag":'
                            . "\n" . implode("\n", $issues) . "\n"
                    );
                }
            },
            \Magento\TestFramework\Utility\Files::init()->getLayoutFiles()
        );
    }

    public function testLayoutFormat()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            /**
             * Test format of a layout file using XSD
             *
             * @param string $layoutFile
             */
            function ($layoutFile) {
                $schemaFile = BP . '/app/code/Magento/Core/etc/layout_single.xsd';
                $domLayout = new \Magento\Config\Dom(file_get_contents($layoutFile));
                $result = $domLayout->validate($schemaFile, $errors);
                $this->assertTrue($result, print_r($errors, true));
            },
            \Magento\TestFramework\Utility\Files::init()->getLayoutFiles()
        );
    }
}
