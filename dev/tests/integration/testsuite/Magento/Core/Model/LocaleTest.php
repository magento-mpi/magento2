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
        $objectManager = Magento_Core_Model_ObjectManager::getInstance();
        Zend_Locale_Data::removeCache();
        $this->assertNull(Zend_Locale_Data::getCache());
        $model = $objectManager->create('Magento_Core_Model_Locale', array('locale' => 'some_locale'));
        $this->assertInstanceOf('Zend_Locale', $model->getLocale());
        $this->assertInstanceOf('Zend_Cache_Core', Zend_Locale_Data::getCache());
    }
}
