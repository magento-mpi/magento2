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

class Integrity_Modular_LayoutFilesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    public function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
    }

    /**
     * @dataProvider validateDataProvider
     */
    public function testLayouts($layout)
    {
        $modulesReader = $this->_objectManager->get('Magento_Core_Model_Config_Modules_Reader');
        $domLayout = $this->_objectManager->create('Magento_Config_Dom', array('xml' => file_get_contents($layout)));
        $result = $domLayout->validate(
            $modulesReader->getModuleDir('etc', 'Mage_Core') . DIRECTORY_SEPARATOR . 'layouts.xsd', $errors
        );
        $this->assertTrue($result, print_r($errors, true));
    }

    /**
     * @see self::testValidateLayouts
     * @return array
     * @throws Exception
     */
    public function validateDataProvider()
    {
        $patterns = array(
            Mage::getBaseDir('app') . '/*/*/*/*/*/layout/*.xml',
            Mage::getBaseDir('app') . '/*/*/*/*/*/layout/*/*.xml',
            Mage::getBaseDir('app') . '/*/*/*/*/*/layout/*/*/*/*.xml',
            Mage::getBaseDir('app') . '/*/*/*/*/*/layout/*/*/*/*/*.xml',
            Mage::getBaseDir('app') . '/*/*/*/*/*/layout/*/*/*/*/*/*.xml'
        );
        $layouts = array();
        foreach ($patterns as $pattern) {
            $layouts = array_merge($layouts, glob($pattern));
        }

        if (empty($layouts)) {
            throw new Exception("No layouts found");
        }

        return array_map(function($layout) {
            return array($layout);
        }, $layouts);
    }
}
