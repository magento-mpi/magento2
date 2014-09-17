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
 * Class NewProductsTest
 * @package Magento\Catalog\Model\Rss\Product
 */
class NewProductsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Rss\Product\NewProducts
     */
    protected $newProducts;

    /**
     * @var ObjectManagerHelper
     */
    protected $objectManagerHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $visibility;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $timezoneInterface;

    protected function setUp()
    {
        $this->productFactory = $this->getMock('Magento\Catalog\Model\ProductFactory');
        $this->visibility = $this->getMock('Magento\Catalog\Model\Product\Visibility', [], [], '', false);
        $this->timezoneInterface = $this->getMock('Magento\Framework\Stdlib\DateTime\TimezoneInterface');

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->newProducts = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Model\Rss\Product\NewProducts',
            [
                'productFactory' => $this->productFactory,
                'visibility' => $this->visibility,
                'localeDate' => $this->timezoneInterface
            ]
        );
    }

    public function testGetProductsCollection()
    {
    }
}
