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

class Integrity_Modular_MenuConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_Config_Reader
     */
    protected $_model;

    public function setUp()
    {
        $moduleReader = Mage::getObjectManager()->create('Mage_Core_Model_Config_Modules_Reader');
        $schemaFile = $moduleReader->getModuleDir('etc', 'Mage_Backend') . DIRECTORY_SEPARATOR . 'menu.xsd';
        $this->_model = Mage::getObjectManager()->create('Mage_Backend_Model_Menu_Config_Reader',
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
