<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Resource_WebsiteTest extends PHPUnit_Framework_TestCase
{
    public function testCountAll()
    {
        /** @var $model \Magento\Core\Model\Resource\Website */
        $model = Mage::getModel('\Magento\Core\Model\Resource\Website');
        $this->assertEquals(1, $model->countAll());
        $this->assertEquals(1, $model->countAll(false));
        $this->assertEquals(2, $model->countAll(true));
    }
}
