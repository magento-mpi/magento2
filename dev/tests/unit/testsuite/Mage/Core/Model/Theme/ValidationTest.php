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
 * Theme data validation
 */
class Mage_Core_Model_Theme_ValidationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test validator with valid data
     *
     * @covers Mage_Core_Model_Theme_Validator::validate
     */
    public function testValidateWithValidData()
    {
        /** @var $themeMock Varien_Object */
        $themeMock = new Varien_Object();
        $themeMock->setData($this->_getThemeValidData());

        /** @var $validatorMock Mage_Core_Model_Theme_Validator */
        $validatorMock = $this->getMock(
            'Mage_Core_Model_Theme_Validator', array('_setThemeValidators'), array(), '', false
        );

        $versionValidators = array(
            array(
                'name' => 'available', 'class' => 'Zend_Validate_Regex', 'break' => true,
                'options' => array('pattern' => '/([a-z0-9\_]+)/'),
                'message' => 'Theme code has not compatible format'
            )
        );

        $validatorMock->addDataValidators('theme_code', $versionValidators);
        $this->assertEquals(true, $validatorMock->validate($themeMock));
    }

    /**
     * Test validator with invalid data
     *
     * @covers Mage_Core_Model_Theme_Validator::validate
     */
    public function testValidateWithInvalidData()
    {
        /** @var $themeMock Varien_Object */
        $themeMock = new Varien_Object();
        $themeMock->setData($this->_getThemeInvalidData());

        /** @var $validatorMock Mage_Core_Model_Theme_Validator */
        $validatorMock = $this->getMock(
            'Mage_Core_Model_Theme_Validator', array('_setThemeValidators'), array(), '', true
        );

        $codeValidators = array(
            array(
                'name' => 'available', 'class' => 'Zend_Validate_Regex', 'break' => true,
                'options' => array('pattern' => '/^[a-z]+$/'),
                'message' => 'Theme code has not compatible format'
            ),
        );

        $versionValidators = array(
            array(
                'name' => 'available', 'class' => 'Zend_Validate_Regex', 'break' => true,
                'options' => array('pattern' => '/(\d+\.\d+\.\d+\.\d+(\-[a-zA-Z0-9]+)?)|\*/'),
                'message' => 'Theme version has not compatible format'
            )
        );

        $validatorMock->addDataValidators('theme_code', $codeValidators)
            ->addDataValidators('theme_version', $versionValidators)
            ->addDataValidators('magento_version_from', $versionValidators);
        $this->assertEquals(false, $validatorMock->validate($themeMock));
        $this->assertEquals($this->_getErrorMessages(), $validatorMock->getErrorMessages());
    }

    /**
     * Get theme valid data
     *
     * @return array
     */
    protected function _getThemeValidData()
    {
        return array(
            'theme_code'           => 'iphone',
            'theme_title'          => 'Iphone',
            'theme_version'        => '2.0.0.0',
            'parent_theme'         => array('default', 'default'),
            'magento_version_from' => '2.0.0.0-dev1',
            'magento_version_to'   => '*',
            'theme_path'           => 'default/iphone',
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
            'theme_code'           => 'iphone#theme!!!!',
            'theme_title'          => 'Iphone',
            'theme_version'        => 'last theme version',
            'parent_theme'         => array('default', 'default'),
            'magento_version_from' => 'new version',
            'magento_version_to'   => '*',
            'theme_path'           => 'default/iphone',
            'preview_image'        => 'images/preview.png',
        );
    }

    /**
     * Get error messages
     *
     * @return array
     */
    protected function _getErrorMessages()
    {
        return array(
            'magento_version_from' => array('Theme version has not compatible format'),
            'theme_code'           => array('Theme code has not compatible format'),
            'theme_version'        => array('Theme version has not compatible format')
        );
    }
}