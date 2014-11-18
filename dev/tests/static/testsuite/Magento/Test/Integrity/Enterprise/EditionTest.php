<?php
/**
 * Check whether enterprise module.xml.dist exactly corresponds to /app/code/Enterprise contents
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Integrity\Enterprise;

class EditionTest extends \PHPUnit_Framework_TestCase
{
    public function testCongruence()
    {
        $root = \Magento\Framework\Test\Utility\Files::init()->getPathToSource();

        $xmlFile = $root . '/app/etc/enterprise/module.xml.dist';
        $dom = new \DOMDocument();
        $dom->loadXML(file_get_contents($xmlFile));
        $xpath = new \DOMXPath($dom);
        $moduleNames = array();
        /** @var $moduleNode \DOMNode */
        foreach ($xpath->query('/config/module') as $moduleNode) {
            $nameNode = $moduleNode->attributes->getNamedItem('name');
            $activeNode = $moduleNode->attributes->getNamedItem('active');
            $this->assertNotNull($nameNode);
            $this->assertNotNull($activeNode);
            $moduleName = $nameNode->nodeValue;
            $this->assertFalse(
                in_array($moduleName, $moduleNames),
                "Module {$moduleName} appears more than once in {$xmlFile} file."
            );
            $moduleNames[] = $moduleName;
        }

        $magentoPoolPath = $root . '/app/code/Magento';
        foreach (new \DirectoryIterator($magentoPoolPath) as $dir) {
            if (!$dir->isDot()) {
                $moduleName = 'Magento_' . $dir->getFilename();
                $moduleNames = array_diff($moduleNames, array($moduleName));
            }
        }

        $this->assertEquals(
            0,
            count($moduleNames),
            implode(', ', $moduleNames) . " module(s) not found in {$magentoPoolPath}."
        );
    }
}
