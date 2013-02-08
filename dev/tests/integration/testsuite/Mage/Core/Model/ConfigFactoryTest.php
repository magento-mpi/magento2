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
 * Second part of Mage_Core_Model_Config testing:
 * - Mage factory behaviour is tested
 *
 * @see Mage_Core_Model_ConfigTest
 */
class Mage_Core_Model_ConfigFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Config */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Mage_Core_Model_Config');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testGetModelInstance()
    {
        $this->assertInstanceOf('Mage_Core_Model_Config', $this->_model->getModelInstance('Mage_Core_Model_Config'));
    }

    public function testGetResourceModelInstance()
    {
        $this->assertInstanceOf(
            'Mage_Core_Model_Resource_Config',
            $this->_model->getResourceModelInstance('Mage_Core_Model_Resource_Config')
        );
    }
}
