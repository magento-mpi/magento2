<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Enterprise
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for resource setup model needed for migration process between Magento versions in Enterprise version
 */
class Magento_Enterprise_Model_Resource_Setup_MigrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\Enterprise\Model\Resource\Setup\Migration::getCompositeModules
     */
    public function testGetCompositeModules()
    {
        $compositeModules = \Magento\Enterprise\Model\Resource\Setup\Migration::getCompositeModules();
        $this->assertInternalType('array', $compositeModules);
        $this->assertNotEmpty($compositeModules);
        foreach ($compositeModules as $classAlias => $className) {
            $this->assertInternalType('string', $classAlias);
            $this->assertInternalType('string', $className);
            $this->assertNotEmpty($classAlias);
            $this->assertNotEmpty($className);
        }

        // array must contain all data from parent class
        $parentModules = \Magento\Core\Model\Resource\Setup\Migration::getCompositeModules();
        $this->assertEmpty(array_diff($parentModules, $compositeModules));
    }
}
