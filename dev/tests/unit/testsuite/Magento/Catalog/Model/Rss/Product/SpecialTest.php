<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Rss\Product;

use \Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

/**
 * Class SpecialTest
 * @package Magento\Catalog\Model\Rss\Product
 */
class SpecialTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Rss\Product\Special
     */
    protected $special;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerInterface;

    protected function setUp()
    {
        $this->productFactory = $this->getMock('Magento\Catalog\Model\ProductFactory');
        $this->storeManagerInterface = $this->getMock('Magento\Store\Model\StoreManagerInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->special = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Rss\Product\Special',
            [
                'productFactory' => $this->productFactory,
                'storeManager' => $this->storeManagerInterface
            ]
        );
    }

    public function testGetProductsCollection()
    {
    }
}
