<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Export
 */
class Mage_ImportExport_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test customer behaviours getter
     */
    public function testGetAllowedCustomerBehaviours()
    {
        $helper = new Mage_ImportExport_Helper_Data();
        $importVersion = 'test_key';
        $correctBehaviours = array('behaviour_1', 'behaviour_2');

        $reflectionBehaviours = new ReflectionProperty('Mage_ImportExport_Helper_Data', '_allowedCustomerBehaviours');
        $reflectionBehaviours->setAccessible(true);
        $reflectionBehaviours->setValue($helper, array($importVersion => $correctBehaviours));

        $testBehaviours = $helper->getAllowedCustomerBehaviours($importVersion);
        $this->assertEquals($correctBehaviours, $testBehaviours, 'Incorrect behaviours array.');
    }
}
