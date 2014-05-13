<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class TemplateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider setDesignConfigExceptionDataProvider
     * @expectedException \Magento\Framework\Exception
     */
    public function testSetDesignConfigException($config)
    {
        // \Magento\Core\Model\Template is an abstract class
        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Email\Model\Template');
        $model->setDesignConfig($config);
    }

    public function setDesignConfigExceptionDataProvider()
    {
        $storeId = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Store\Model\StoreManagerInterface'
        )->getStore()->getId();
        return array(array(array()), array(array('area' => 'frontend')), array(array('store' => $storeId)));
    }
}
