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

namespace Magento\Core\Model;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider setDesignConfigExceptionDataProvider
     * @expectedException \Magento\Exception
     */
    public function testSetDesignConfigException($config)
    {
        // \Magento\Core\Model\Template is an abstract class
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Email\Template');
        $model->setDesignConfig($config);
    }

    public function setDesignConfigExceptionDataProvider()
    {
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\StoreManagerInterface')->getStore()->getId();
        return array(
            array(array()),
            array(array('area' => 'frontend')),
            array(array('store' => $storeId)),
        );
    }
}
