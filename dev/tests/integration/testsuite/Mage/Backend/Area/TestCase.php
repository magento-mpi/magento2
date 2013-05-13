<?php
/**
 * A parent class for backend area entities - contains directives for backend area configuration loading
 *
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
class Mage_Backend_Area_TestCase extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        Mage::app()->loadArea(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        Mage::getConfig()->setCurrentAreaCode(Mage_Core_Model_App_Area::AREA_ADMINHTML);
        parent::setUpBeforeClass();
    }
}

