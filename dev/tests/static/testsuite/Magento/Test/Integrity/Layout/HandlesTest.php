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
                $label = $node['label'];
                $design_abstraction = $node['design_abstraction'];
                if (!$label) {
                    if ($design_abstraction) {
                        $issues[] = 'Attribute "design_abstraction" is defined, but "label" is not';
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
                /** @var \SimpleXMLElement $node */
                foreach ($containers as $node) {
                    if (!isset($node['htmlTag']) && (isset($node['htmlId']) || isset($node['htmlClass']))) {
                        $issues[] = $node->asXML();
                    }
                }
                if ($issues) {
                    $this->fail(
                        'The following containers declare attribute "htmlId" and/or "htmlClass", but not "htmlTag":' .
                        "\n" .
                        implode(
                            "\n",
                            $issues
                        ) . "\n"
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
