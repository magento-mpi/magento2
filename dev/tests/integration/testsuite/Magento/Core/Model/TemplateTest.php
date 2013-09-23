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

class Magento_Core_Model_TemplateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider setDesignConfigExceptionDataProvider
     * @expectedException Magento_Exception
     */
    public function testSetDesignConfigException($config)
    {
        // Magento_Core_Model_Template is an abstract class
        $model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Core_Model_Email_Template');
        $model->setDesignConfig($config);
    }

    public function setDesignConfigExceptionDataProvider()
    {
        $storeId = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->get('Magento_Core_Model_StoreManagerInterface')->getStore()->getId();
        return array(
            array(array()),
            array(array('area' => 'frontend')),
            array(array('store' => $storeId)),
        );
    }
}
