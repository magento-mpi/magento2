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

class Magento_Test_Integrity_Modular_MenuConfigFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Menu\Config\Reader
     */
    protected $_model;

    public function setUp()
    {
        $moduleReader = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Config\Modules\Reader');
        $schemaFile = $moduleReader->getModuleDir('etc', 'Magento_Backend') . DIRECTORY_SEPARATOR . 'menu.xsd';
        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento\Backend\Model\Menu\Config\Reader',
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
