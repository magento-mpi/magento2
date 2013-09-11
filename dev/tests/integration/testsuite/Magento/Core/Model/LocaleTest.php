<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Magento_Core_Model_Locale class
 */
class Magento_Core_Model_LocaleTest extends PHPUnit_Framework_TestCase
{
    public function testGetLocale()
    {
        Zend_Locale_Data::removeCache();
        $this->assertNull(Zend_Locale_Data::getCache());
        $model = new Magento_Core_Model_Locale(
            Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Store_Config'),
            'some_locale'
        );
        $this->assertInstanceOf('Zend_Locale', $model->getLocale());
        $this->assertInstanceOf('Zend_Cache_Core', Zend_Locale_Data::getCache());
    }
}
