<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Varien_Data_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testRemoveAllItems()
    {
        $model = new Varien_Data_Collection();
        $model->addItem(new Varien_Object());
        $model->addItem(new Varien_Object());
        $this->assertCount(2, $model->getItems());
        $model->removeAllItems();
        $this->assertEmpty($model->getItems());
    }
}
