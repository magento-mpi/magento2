<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Class CmsBlock
 * CMS Block fixture
 */
class CmsBlock extends InjectableFixture
{
    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Cms\Test\Handler\CmsBlock\CmsBlockInterface';

    protected $defaultDataSet = [
        'title' => 'block_%isolation%',
        'identifier' => 'identifier_%isolation%',
        'stores' => ['dataSet' => ['All Store Views']],
        'is_active' => 'Enabled',
        'content' => 'description_%isolation%',
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

    protected $stores = [
        'attribute_code' => 'stores',
        'backend_type' => 'virtual',
        'is_required' => '1',
        'default_value' => '0',
        'input' => 'multiselectgrouplist',
        'source' => 'Magento\Cms\Test\Fixture\CmsBlock\Stores',
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

    public function getStores()
    {
        return $this->getData('stores');
    }
}
