<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CmsBlock
 */
class CmsBlock extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Cms\Test\Repository\CmsBlock';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Cms\Test\Handler\CmsBlock\CmsBlockInterface';

    protected $defaultDataSet = [
        'is_active' => null,
    ];

    protected $block_id = [
        'attribute_code' => 'block_id',
        'backend_type' => 'smallint',
        'is_required' => '1',
        'default_value' => '',
        'input' => '',
    ];

    protected $title = [
        'attribute_code' => 'title',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $identifier = [
        'attribute_code' => 'identifier',
        'backend_type' => 'varchar',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $content = [
        'attribute_code' => 'content',
        'backend_type' => 'mediumtext',
        'is_required' => '',
        'default_value' => '',
        'input' => 'text',
    ];

    protected $creation_time = [
        'attribute_code' => 'creation_time',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $update_time = [
        'attribute_code' => 'update_time',
        'backend_type' => 'timestamp',
        'is_required' => '',
        'default_value' => '',
        'input' => '',
    ];

    protected $is_active = [
        'attribute_code' => 'is_active',
        'backend_type' => 'smallint',
        'is_required' => '',
        'default_value' => '1',
        'input' => 'select',
    ];

    protected $store_id = [
        'attribute_code' => 'store_id',
        'backend_type' => 'virtual',
        'is_required' => '1',
        'default_value' => '0',
        'input' => 'multiselectgrouplist',
        'source' => 'Magento\Cms\Test\Fixture\CmsBlock\StoreId',
    ];

    public function getBlockId()
    {
        return $this->getData('block_id');
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function getIdentifier()
    {
        return $this->getData('identifier');
    }

    public function getContent()
    {
        return $this->getData('content');
    }

    public function getCreationTime()
    {
        return $this->getData('creation_time');
    }

    public function getUpdateTime()
    {
        return $this->getData('update_time');
    }

    public function getIsActive()
    {
        return $this->getData('is_active');
    }

    public function getStoreId()
    {
        return $this->getData('store_id');
    }
}
