<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Data_CollectionTest extends PHPUnit_Framework_TestCase
{
    public function testRemoveAllItems()
    {
        $model = new Magento_Data_Collection(
            $this->getMock('Magento_Core_Model_EntityFactory', array(), array(), '', false)
        );
        $model->addItem(new Magento_Object());
        $model->addItem(new Magento_Object());
        $this->assertCount(2, $model->getItems());
        $model->removeAllItems();
        $this->assertEmpty($model->getItems());
    }
}
