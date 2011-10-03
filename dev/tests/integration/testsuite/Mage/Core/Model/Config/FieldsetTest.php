<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Core
 */
class Mage_Core_Model_Config_FieldsetTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        /* Generate and save cache */
        Mage::app()->getCache()->remove('fieldset_config');
        $config = new Mage_Core_Model_Config_Fieldset;
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $config->getNode());

        /* Load from cache */
        $this->assertTrue(Mage::app()->useCache('config'));
        $config = new Mage_Core_Model_Config_Fieldset;
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $config->getNode());

        /* Generate and not save */
        Mage::app()->getCacheInstance()->banUse('config');
        $this->assertFalse(Mage::app()->useCache('config'));
        $config = new Mage_Core_Model_Config_Fieldset;
        $this->assertInstanceOf('Mage_Core_Model_Config_Element', $config->getNode());
    }
}
