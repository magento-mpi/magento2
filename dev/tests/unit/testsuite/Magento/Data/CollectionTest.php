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
        $model = new \Magento\Data\Collection();
        $model->addItem(new \Magento\Object());
        $model->addItem(new \Magento\Object());
        $this->assertCount(2, $model->getItems());
        $model->removeAllItems();
        $this->assertEmpty($model->getItems());
    }
}
