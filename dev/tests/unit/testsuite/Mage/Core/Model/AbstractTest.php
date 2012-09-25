<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Authorization.
 */
class Mage_Core_Model_AbstractTest extends PHPUnit_Framework_TestCase
{
    public function testSetGetSerializable()
    {
        $this->assertClassHasStaticAttribute('_isSerializable', 'Mage_Core_Model_Abstract');

        Mage_Core_Model_Abstract::setIsSerializable(false);
        $this->assertFalse(Mage_Core_Model_Abstract::getIsSerializable());

        Mage_Core_Model_Abstract::setIsSerializable(true);
        $this->assertTrue(Mage_Core_Model_Abstract::getIsSerializable());

        // incorrect data
        Mage_Core_Model_Abstract::setIsSerializable('random_string');
        $this->assertTrue(Mage_Core_Model_Abstract::getIsSerializable());
    }
}
