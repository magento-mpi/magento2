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

class Integrity_Modular_MenuConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Menu_Config_Reader
     */
    protected $_model;

    public function setUp()
    {
        $moduleReader = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Config_Modules_Reader');
        $schemaFile = $moduleReader->getModuleDir('etc', 'Magento_Backend') . DIRECTORY_SEPARATOR . 'menu.xsd';
        $this->_model = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Backend_Model_Menu_Config_Reader',
            array(
                'perFileSchema' => $schemaFile,
                'isValidated' => true,
            )
        );
    }

    public function testValidateMenuFiles()
    {
        $this->_model->read('adminhtml');
    }
}
