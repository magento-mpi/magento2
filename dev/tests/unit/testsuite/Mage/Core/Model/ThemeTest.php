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
     */
    public function testLoadFromConfiguration()
    {
        $themePath = __DIR__ . '/_files/theme/theme.xml';

        /** @var $themeMock Mage_Core_Model_Theme */
        $themeMock = $this->getMock('Mage_Core_Model_Theme', array('_init'), array(), '', true);
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
            'magento_version_from' => '2.0.0.1-dev1',
            'magento_version_to'   => '*',
            'theme_path'           => 'default/iphone',
            'preview_image'        => '',
        );
    }
}
