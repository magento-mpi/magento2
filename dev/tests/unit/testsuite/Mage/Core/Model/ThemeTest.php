<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test theme model
 */
class Mage_Core_Model_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test load from configuration
     *
     * @covers Mage_Core_Model_Theme::loadFromConfiguration
     */
    public function testLoadFromConfiguration()
    {
        $themePath = implode(DS, array(__DIR__, '_files', 'frontend', 'default', 'iphone', 'theme.xml'));
        $designDir = implode(DS, array(__DIR__, '_files'));
        Mage::getConfig()->getOptions()->setData('design_dir', $designDir);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        /** @var $themeMock Mage_Core_Model_Theme */
        $arguments = $objectManagerHelper->getConstructArguments(Magento_Test_Helper_ObjectManager::MODEL_ENTITY);
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('_init'), $arguments, '', true);
        $themeMock->loadFromConfiguration($themePath);

        $this->assertEquals($this->_expectedThemeDataFromConfiguration(), $themeMock->getData());
    }

    /**
     * Test load invalid configuration
     *
     * @covers Mage_Core_Model_Theme::loadFromConfiguration
     * @expectedException Magento_Exception
     */
    public function testLoadInvalidConfiguration()
    {
        $themePath = implode(DS, array(__DIR__, '_files', 'frontend', 'default', 'iphone', 'theme_invalid.xml'));
        $designDir = implode(DS, array(__DIR__, '_files'));
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        Mage::getConfig()->getOptions()->setData('design_dir', $designDir);

        /** @var $themeMock Mage_Core_Model_Theme */
        $arguments = $objectManagerHelper->getConstructArguments(Magento_Test_Helper_ObjectManager::MODEL_ENTITY);
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('_init'), $arguments, '', true);
        $themeMock->loadFromConfiguration($themePath);

        $this->assertEquals($this->_expectedThemeDataFromConfiguration(), $themeMock->getData());
    }

    /**
     * Expected theme data from configuration
     *
     * @return array
     */
    public function _expectedThemeDataFromConfiguration()
    {
        return array(
            'parent_id'            => null,
            'theme_path'           => 'default/iphone',
            'theme_version'        => '2.0.0.1',
            'theme_title'          => 'Iphone',
            'preview_image'        => 'images/preview.png',
            'magento_version_from' => '2.0.0.1-dev1',
            'magento_version_to'   => '*',
            'is_featured'          => '1',
            'theme_directory'      => implode(DS, array(__DIR__, '_files', 'frontend', 'default', 'iphone')),
            'parent_theme_path'    => null,
            'area'                 => 'frontend',
            'package_code'         => 'default',
            'theme_code'           => 'iphone',
        );
    }
}
