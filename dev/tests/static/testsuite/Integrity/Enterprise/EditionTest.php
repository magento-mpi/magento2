<?php
/**
 * Check whether XEnterprise_Edition.xml.dist exactly corresponds to /app/code/Enterprise contents
 *
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Integrity
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Integrity_Enterprise_EditionTest extends PHPUnit_Framework_TestCase
{
    public function testCongruence()
    {
        $root = Utility_Files::init()->getPathToSource();

        $xmlFile = $root . '/app/etc/enterprise/modules.xml.dist';
        $xml = simplexml_load_file($xmlFile);
        $xmlModuleNodes = $xml->xpath('/config/modules');
        $this->assertEquals(1, count($xmlModuleNodes));

        $modules = array();
        foreach (reset($xmlModuleNodes) as $moduleName => $node) {
            $this->assertObjectHasAttribute('active', $node);
            $this->assertFalse(isset($modules[$moduleName]), "$moduleName module appears more than once in $xmlFile");
            $modules[$moduleName] = 1;
        }

        $poolPath = $root . '/app/code/Enterprise';
        foreach (new DirectoryIterator($poolPath) as $dir) {
            if (!$dir->isDot()) {
                $moduleName = 'Enterprise_' . $dir->getFilename();
                $this->assertTrue(isset($modules[$moduleName]), "$moduleName module not found in $xmlFile");
                unset($modules[$moduleName]);
            }
        }

        $this->assertEquals(
            0,
            count($modules),
            implode(', ', array_keys($modules)) . " module(s) not found in $poolPath"
        );
    }
}
