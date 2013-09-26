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

/**
 * Test theme data validator
 */
class Magento_Core_Model_Theme_ValidatorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test validator with valid data
     */
    public function testValidateWithValidData()
    {
        /** @var $validator Magento_Core_Model_Theme_Validator */
        $validator = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Theme_Validator');

        $themeModel = $this->_getThemeModel();
        $themeModel->setData($this->_getThemeValidData());

        $this->assertEquals(true, $validator->validate($themeModel));
    }

    /**
     * Test validator with invalid data
     */
    public function testValidateWithInvalidData()
    {
        /** @var $validator Magento_Core_Model_Theme_Validator */
        $validator = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Theme_Validator');

        $themeModel = $this->_getThemeModel();
        $themeModel->setData($this->_getThemeInvalidData());

        $this->assertEquals(false, $validator->validate($themeModel));
    }

    /**
     * Get theme model
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _getThemeModel()
    {
        return Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Theme');
    }

    /**
     * Get theme valid data
     *
     * @return array
     */
    protected function _getThemeValidData()
    {
        return array(
            'theme_code'           => 'space',
            'theme_title'          => 'Space theme',
            'theme_version'        => '2.0.0.0',
            'parent_theme'         => null,
            'theme_path'           => 'default/space',
            'preview_image'        => 'images/preview.png',
        );
    }

    /**
     * Get theme invalid data
     *
     * @return array
     */
    protected function _getThemeInvalidData()
    {
        return array(
            'theme_code'           => 'space',
            'theme_title'          => 'Space theme',
            'theme_version'        => 'last version',
            'parent_theme'         => null,
            'theme_path'           => 'default/space',
            'preview_image'        => 'images/preview.png',
        );
    }
}
