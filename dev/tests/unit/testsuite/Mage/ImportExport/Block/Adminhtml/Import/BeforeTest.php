<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for block Mage_ImportExport_Block_Adminhtml_Import_BeforeTest
 */
class Mage_ImportExport_Block_Adminhtml_Import_BeforeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested source model
     *
     * @var Mage_ImportExport_Model_Source_Format_Version
     */
    public static $sourceModel;

    /**
     * Helper registry key
     *
     * @var string
     */
    protected static $_helperKey = '_helper/Mage_ImportExport_Helper_Data';

    /**
     * Mock helper
     *
     * @static
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        Mage::unregister(self::$_helperKey);
        Mage::register(self::$_helperKey, new Mage_ImportExport_Helper_Data());
    }

    /**
     * Unregister helper
     *
     * @static
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        Mage::unregister(self::$_helperKey);
    }

    /**
     * Test getter for JS array behaviour string
     */
    public function testGetJsAllowedCustomerBehaviours()
    {
        /** @var $helper Mage_ImportExport_Helper_Data */
        $helper = Mage::helper('Mage_ImportExport_Helper_Data');
        $existingVersion = 'existing_version';
        $customerBehaviours = array('behaviour_1', 'behaviour_2');
        $notExistingVersion = 'not_existing_version';

        $reflectionBehaviours = new ReflectionProperty('Mage_ImportExport_Helper_Data', '_allowedCustomerBehaviours');
        $reflectionBehaviours->setAccessible(true);
        $reflectionBehaviours->setValue($helper, array($existingVersion => $customerBehaviours));

        $block = new Mage_ImportExport_Block_Adminhtml_Import_Before();

        $testJsBehaviours = $block->getJsAllowedCustomerBehaviours($existingVersion);
        $correctJsBehaviours = '["' . implode('", "', $customerBehaviours) . '"]';
        $this->assertEquals($correctJsBehaviours, $testJsBehaviours, 'Incorrect JS array string.');

        $testJsBehaviours = $block->getJsAllowedCustomerBehaviours($notExistingVersion);
        $correctJsBehaviours = '[]';
        $this->assertEquals($correctJsBehaviours, $testJsBehaviours, 'Incorrect JS array string.');
    }
}
