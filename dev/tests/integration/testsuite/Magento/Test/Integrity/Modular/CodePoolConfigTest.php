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
class Magento_Test_Integrity_Modular_CodePoolConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    protected function setUp()
    {
        $this->_config = Mage::getSingleton('Magento_Core_Model_Config');
    }

    public function testCodePoolConfigNode()
    {
        $result = array();
        $modulesConfig = $this->_config->getNode('modules');
        /** @var $moduleConfig Magento_Simplexml_Element */
        foreach ($modulesConfig->children() as $moduleConfig) {
            if (array_key_exists('codePool', $moduleConfig->asArray())) {
                $result[] = $moduleConfig->getName();
            }
        }
        $this->assertEquals(array(), $result, 'Specified modules contain obsolete codePool configuration');
    }
}
