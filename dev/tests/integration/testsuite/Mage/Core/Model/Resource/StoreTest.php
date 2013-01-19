<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Resource_StoreTest extends PHPUnit_Framework_TestCase
{
    /**
     * By default out of the box there are 2 store views: one for admin, another for frontend
     */
    public function testCountAll()
    {
        $this->assertEquals(2, Mage::getModel('Mage_Core_Model_Resource_Store')->countAll());
    }
}
