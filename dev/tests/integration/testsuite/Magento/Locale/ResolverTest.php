<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Locale;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLocale()
    {
        $objectManager = \Magento\App\ObjectManager::getInstance();
        \Zend_Locale_Data::removeCache();
        $this->assertNull(\Zend_Locale_Data::getCache());
        $model = $objectManager->create('Magento\Locale\ResolverInterface', array('locale' => 'some_locale'));
        $this->assertInstanceOf('Zend_Locale', $model->getLocale());
        $this->assertInstanceOf('Zend_Cache_Core', \Zend_Locale_Data::getCache());
    }
}
