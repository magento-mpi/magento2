<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class Mage_Backend_Controller_Router_Default
 */
class Mage_Backend_Controller_Router_Validator_DefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoConfigFixture global/areas/adminhtml/frontName 0
     * @expectedException InvalidArgumentException
     * @magentoAppIsolation enabled
     */
    public function testConstructWithEmptyAreaFrontName()
    {
        $options = array(
            'area' => Mage::helper('Mage_Backend_Helper_Data')->getAreaCode(),
            'base_controller' => 'Mage_Backend_Controller_ActionAbstract',
            'frontName' => 'backend'
        );
        new Mage_Backend_Controller_Router_Default($options);
    }

    /**
     * @magentoConfigFixture global/areas/adminhtml/frontName backend
     * @magentoAppIsolation enabled
     */
    public function testConstructWithNotEmptyAreaFrontName()
    {
        $options = array(
            'area' => Mage::helper('Mage_Backend_Helper_Data')->getAreaCode(),
            'base_controller' => 'Mage_Backend_Controller_ActionAbstract',
            'frontName' => 'backend'
        );
        new Mage_Backend_Controller_Router_Default($options);
    }
}
