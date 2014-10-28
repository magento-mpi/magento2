<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Option\Type\File;

class ValidatorInfoTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ValidatorInfo
     */
    protected $model;

    /**
     * @var \Magento\Framework\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $this->objectManager->create('Magento\Catalog\Model\Product\Option\Type\File\ValidatorInfo');
    }

    public function testValidate()
    {
        $this->model->validate([], $this->getProductOption());
    }

    /**
     * @return \Magento\Catalog\Model\Product\Option
     */
    protected function getProductOption()
    {
        $option = $this->objectManager->create(
            'Magento\Catalog\Model\Product\Option',
            [
                'option_id' => '1',
                'product_id' => '4',
                'type' => 'file',
                'is_require' => '1',
                'sku' => null,
                'max_characters' => null,
                'file_extension' => null,
                'image_size_x' => '2000',
                'image_size_y' => '2000',
                'sort_order' => '0',
                'default_title' => 'MediaOption',
                'store_title' => null,
                'title' => 'MediaOption',
                'default_price' => '5.0000',
                'default_price_type' => 'fixed',
                'store_price' => null,
                'store_price_type' => null,
                'price' => '5.0000',
                'price_type' => 'fixed',
            ]
        );

        return $option;
    }
}
