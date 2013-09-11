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
        /** @var $validator \Magento\Core\Model\Theme\Validator */
        $validator = Mage::getModel('\Magento\Core\Model\Theme\Validator');

        $themeModel = $this->_getThemeModel();
        $themeModel->setData($this->_getThemeValidData());

        $this->assertEquals(true, $validator->validate($themeModel));
    }

    /**
     * Test validator with invalid data
     */
    public function testValidateWithInvalidData()
    {
        /** @var $validator \Magento\Core\Model\Theme\Validator */
        $validator = Mage::getModel('\Magento\Core\Model\Theme\Validator');

        $themeModel = $this->_getThemeModel();
        $themeModel->setData($this->_getThemeInvalidData());

        $this->assertEquals(false, $validator->validate($themeModel));
    }

    /**
     * Get theme model
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _getThemeModel()
    {
        return Mage::getModel('\Magento\Core\Model\Theme');
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
