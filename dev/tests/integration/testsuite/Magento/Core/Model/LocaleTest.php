<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for \Magento\Core\Model\Locale class
 */
class Magento_Core_Model_LocaleTest extends PHPUnit_Framework_TestCase
{
    public function testGetLocale()
    {
        Zend_Locale_Data::removeCache();
        $this->assertNull(Zend_Locale_Data::getCache());
        $model = Magento_TestFramework_ObjectManager::getInstance()->create('Magento\Core\Model\Locale',
            array('locale' => 'some_locale'));
        $this->assertInstanceOf('Zend_Locale', $model->getLocale());
        $this->assertInstanceOf('Zend_Cache_Core', Zend_Locale_Data::getCache());
    }
}
