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
        $themePath = implode(DIRECTORY_SEPARATOR, array(__DIR__, '_files', 'theme', 'theme.xml'));

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
        $themePath = implode(DIRECTORY_SEPARATOR, array(__DIR__, '_files', 'theme', 'theme_invalid.xml'));
        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);

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
            'theme_code'           => 'iphone',
            'theme_title'          => 'Iphone',
            'theme_version'        => '2.0.0.1',
            'parent_theme'         => null,
            'is_featured'          => true,
            'magento_version_from' => '2.0.0.1-dev1',
            'magento_version_to'   => '*',
            'theme_path'           => 'default/iphone',
            'preview_image'        => 'images/preview.png',
            'theme_directory'      => implode(DIRECTORY_SEPARATOR, array(__DIR__, '_files', 'theme'))
        );
    }
}
