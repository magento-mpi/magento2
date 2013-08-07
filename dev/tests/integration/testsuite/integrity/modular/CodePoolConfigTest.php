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
class Integrity_Modular_CodePoolConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    protected function setUp()
    {
        $this->_config = Mage::getSingleton('Mage_Core_Model_Config');
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
