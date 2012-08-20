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
     */
    protected $_themeModel;

    /**
     * Prepare theme model data
     */
    protected function setUp()
    {
        $data = array(
            'package_code'         => 'default',
            'package_title'        => 'Default',
            'parent_theme'         => 'default',
            'theme_code'           => 'iphone',
            'theme_version'        => '2.0.0.0',
            'theme_title'          => 'Iphone',
            'magento_version_from' => '2.0.0.0-dev1',
            'magento_version_to'   => '*',
        );

        $this->_themeModel = new Mage_Core_Model_Theme();
        $this->_themeModel->setData($data);
    }

    /**
     * Test crud operations for theme model
     */
    public function testCrud()
    {
        $crud = new Magento_Test_Entity($this->_themeModel, array('theme_version' => '2.0.0.1'));
        $crud->testCrud();
    }
}
