<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftWrapping\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class GiftWrapping
 */
class GiftWrapping extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\GiftWrapping\Test\Repository\GiftWrapping';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\GiftWrapping\Test\Handler\GiftWrapping\GiftWrappingInterface';

    protected $defaultDataSet = [
        'design' => 'Gift Wrapping %isolation%',
        'website_ids' => ['dataSet' => 'main_website'],
        'status' => 'Enabled',
        'base_price' => 10,
    ];

    protected $wrapping_id = [
        'attribute_code' => 'wrapping_id',
        'backend_type' => 'int',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $status = [
        'attribute_code' => 'status',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $base_price = [
        'attribute_code' => 'base_price',
        'backend_type' => 'decimal',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $image = [
        'attribute_code' => 'image',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $design = [
        'attribute_code' => 'design',
        'backend_type' => 'virtual',
    ];

    protected $website_ids = [
        'attribute_code' => 'website_ids',
        'backend_type' => 'virtual',
        'input' => 'multiselectgrouplist',
        'source' => 'Magento\GiftWrapping\Test\Fixture\GiftWrapping\WebsiteIds',
    ];

    public function getWrappingId()
    {
        return $this->getData('wrapping_id');
    }

    public function getStatus()
    {
        return $this->getData('status');
    }

    public function getBasePrice()
    {
        return $this->getData('base_price');
    }

    public function getImage()
    {
        return $this->getData('image');
    }

    public function getDesign()
    {
        return $this->getData('design');
    }

    public function getWebsiteIds()
    {
        return $this->getData('website_ids');
    }
}
