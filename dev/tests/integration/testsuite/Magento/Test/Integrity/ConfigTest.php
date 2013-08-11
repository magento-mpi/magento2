<?php
/**
 * Configuration integrity tests
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_Integrity_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * Validate cache types in the config
     *
     * @dataProvider cacheTypesDataProvider
     */
    public function testCacheTypes(SimpleXMLElement $node)
    {
        $requiredNodes = array('label', 'description', 'class');
        /** @var $node SimpleXMLElement */
        foreach ($requiredNodes as $requiredNode) {
            $this->assertObjectHasAttribute($requiredNode, $node,
                "Required '$requiredNode' node is not specified for '" . $node->getName() . "' cache type");
        }

        $this->assertTrue(class_exists($node->class),
            "Class '{$node->class}', specified for '" . $node->getName() . "' cache type, doesn't exist");
        $interfaces = class_implements((string) $node->class);
        $this->assertContains('Magento_Cache_FrontendInterface', $interfaces,
            "Class '{$node->class}', specified for '" . $node->getName()
                . "' cache type, must implement 'Magento_Cache_FrontendInterface'"
        );
    }

    /**
     * @return array
     */
    public function cacheTypesDataProvider()
    {
        $config = Mage::app()->getConfig();
        $nodes = array();
        /** @var $node SimpleXMLElement */
        foreach ($config->getXpath('/config/global/cache/types/*') as $node) {
            $nodes[$node->getName()] = array($node);
        }

        return $nodes;
    }
}
