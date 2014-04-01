<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Compare;

class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Compare\Item
     */
    protected $model;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Catalog\Model\Product\Compare\Item');
    }

    protected function tearDown()
    {
        $this->model = null;
    }

    public function testGetIdentities()
    {
        $id = 1;
        $this->model->setId($id);
        $this->assertEquals(
            array(\Magento\Catalog\Model\Product\Compare\Item::CACHE_TAG . '_' . $id),
            $this->model->getIdentities()
        );
    }
}
