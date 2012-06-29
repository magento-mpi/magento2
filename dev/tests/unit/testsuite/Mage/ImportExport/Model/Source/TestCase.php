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
 * Abstract class for import/export source models
 */
abstract class Mage_ImportExport_Model_Source_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Tested source model
     */
    public static $sourceModel;

    /**
     * Helper registry key
     *
     * @var string
     */
    protected static $_helperKey = '_helper/Mage_ImportExport_Helper_Data';

    /**
     * Helper property
     *
     * @var Mage_ImportExport_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected static $_helper;

    /**
     * Unregister source model and helper
     *
     * @static
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        Mage::unregister(self::$_helperKey);
        self::$_helper = null;
        self::$sourceModel = null;
    }

    /**
     * Helper initialization
     *
     * @return Mage_ImportExport_Helper_Data
     */
    protected function _initHelper()
    {
        if (!self::$_helper) {
            self::$_helper = $this->getMock(
                'Mage_ImportExport_Helper_Data',
                array('__')
            );
            self::$_helper->expects($this->any())
                ->method('__')
                ->will($this->returnArgument(0));

            Mage::unregister(self::$_helperKey);
            Mage::register(self::$_helperKey, self::$_helper);
        }
        return self::$_helper;
    }
}
