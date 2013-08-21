<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Second part of Magento_Core_Model_Config testing:
 * - Mage factory behaviour is tested
 *
 * @see Magento_Core_Model_ConfigTest
 */
class Magento_Core_Model_ConfigFactoryTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Model_Config */
    protected $_model;

    public function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Config');
    }

    public function testGetModelInstance()
    {
        $this->assertInstanceOf(
            'Magento_Core_Model_Config',
            $this->_model->getModelInstance('Magento_Core_Model_Config')
        );
    }

    public function testGetResourceModelInstance()
    {
        $this->assertInstanceOf(
            'Magento_Core_Model_Resource_Config',
            $this->_model->getResourceModelInstance('Magento_Core_Model_Resource_Config')
        );
    }
}
