<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Enterprise
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for resource setup model needed for migration process between Magento versions in Enterprise version
 */
class Enterprise_Enterprise_Model_Resource_Setup_MigrationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Enterprise_Enterprise_Model_Resource_Setup_Migration::getCompositeModules
     */
    public function testGetCompositeModules()
    {
        $compositeModules = Enterprise_Enterprise_Model_Resource_Setup_Migration::getCompositeModules();
        $this->assertInternalType('array', $compositeModules);
        $this->assertNotEmpty($compositeModules);
        foreach ($compositeModules as $classAlias => $className) {
            $this->assertInternalType('string', $classAlias);
            $this->assertInternalType('string', $className);
            $this->assertNotEmpty($classAlias);
            $this->assertNotEmpty($className);
        }

        // array must contain all data from parent class
        $parentModules = Magento_Core_Model_Resource_Setup_Migration::getCompositeModules();
        $this->assertEmpty(array_diff($parentModules, $compositeModules));
    }
}
