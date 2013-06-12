<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Resource_WebsiteTest extends PHPUnit_Framework_TestCase
{
    public function testCountAll()
    {
        /** @var $model Mage_Core_Model_Resource_Website */
        $model = Mage::getModel('Mage_Core_Model_Resource_Website');
        $this->assertEquals(1, $model->countAll());
        $this->assertEquals(1, $model->countAll(false));
        $this->assertEquals(2, $model->countAll(true));
    }
}
