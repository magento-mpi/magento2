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

class Mage_Core_Model_ThemeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Theme model
     *
     * @var Mage_Core_Model_Theme
     */
    protected $_themeModel;

    /**
     * Test crud operations for theme model using valid data
     */
    public function testCrud()
    {
        $this->_themeModel = new Mage_Core_Model_Theme();
        $this->_themeModel->setData($this->_getValidData());

        $crud = new Magento_Test_Entity($this->_themeModel, array('theme_version' => '2.0.0.1'));
        $crud->testCrud();
    }

    /**
     * Test crud operations for theme model using invalid data
     *
     * @expectedException Mage_Core_Exception
     */
    public function testCrudWithInvalidData()
    {
        $this->_themeModel = new Mage_Core_Model_Theme();
        $this->_themeModel->setData($this->_getInvalidData());

        $crud = new Magento_Test_Entity($this->_themeModel, array('theme_version' => '2.0.0.1'));
        $crud->testCrud();
    }

    /**
     * Get theme valid data
     *
     * @return array
     */
    protected function _getValidData()
    {
        return array(
            'package_code'         => 'default',
            'package_title'        => 'Default',
            'parent_theme'         => 'default',
            'theme_code'           => 'iphone',
            'theme_version'        => '2.0.0.0',
            'theme_title'          => 'Iphone',
            'magento_version_from' => '2.0.0.0-dev1',
            'magento_version_to'   => '*'
        );
    }

    /**
     * Get theme invalid data
     *
     * @return array
     */
    protected function _getInvalidData()
    {
        return array(
            'package_code'         => 'default',
            'package_title'        => 'Default',
            'parent_theme'         => 'default',
            'theme_code'           => 'iphone',
            'theme_version'        => 'new theme version',
            'theme_title'          => 'Iphone',
            'magento_version_from' => '2.0.0.0-dev1',
            'magento_version_to'   => '*'
        );
    }

}
