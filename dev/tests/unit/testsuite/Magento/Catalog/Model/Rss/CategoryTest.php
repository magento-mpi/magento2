<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Rss;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class CategoryTest
 * @package Magento\Catalog\Model\Rss
 */
class CategoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Rss\Category
     */
    protected $model;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \Magento\Catalog\Model\Layer\Category|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $category;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $visibility;

    protected function setUp()
    {
        $this->category = $this->getMock('Magento\Catalog\Model\Layer\Category', [], [], '', false);
        $this->collectionFactory = $this->getMock('Magento\Catalog\Model\Resource\Product\CollectionFactory');
        $this->visibility = $this->getMock('Magento\Catalog\Model\Product\Visibility', [], [], '', false);

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->model = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Rss\Category',
            [
                'catalogLayer' => $this->category,
                'collectionFactory' => $this->collectionFactory,
                'visibility' => $this->visibility
            ]
        );
    }

    public function testGetProductCollection()
    {
    }
}
