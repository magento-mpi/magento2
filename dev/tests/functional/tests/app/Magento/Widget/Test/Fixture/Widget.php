<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Fixture;

use Mtf\Fixture\InjectableFixture;

/**
 * Fixture for Widget
 *
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class Widget extends InjectableFixture
{
    /**
     * @var string
     */
    protected $repositoryClass = 'Magento\Widget\Test\Repository\Widget';

    /**
     * @var string
     */
    protected $handlerInterface = 'Magento\Widget\Test\Handler\Widget\WidgetInterface';

    protected $defaultDataSet = [
    ];

    protected $code = [
        'attribute_code' => 'code',
        'backend_type' => 'virtual',
        'input' => 'select',
        'group' => 'settings',
    ];

    protected $theme_id = [
        'attribute_code' => 'theme_id',
        'backend_type' => 'virtual',
        'input' => 'select',
        'group' => 'settings',
    ];

    protected $anchor_text = [
        'attribute_code' => 'anchor_text',
        'backend_type' => 'varchar',
    ];

    protected $title = [
        'attribute_code' => 'title',
        'backend_type' => 'varchar',
        'group' => 'frontend_properties',
    ];

    protected $template = [
        'attribute_code' => 'template',
        'backend_type' => 'varchar',
        'input' => 'select',
    ];

    protected $chosen_option = [
        'attribute_code' => 'chosen_option',
        'backend_type' => 'virtual',
        'source' => 'Magento\Widget\Test\Fixture\Widget\ChosenOption',
    ];

    protected $display_type = [
        'attribute_code' => 'display_type',
        'backend_type' => 'varchar',
        'input' => 'select',
    ];

    protected $show_pager = [
        'attribute_code' => 'show_pager',
        'backend_type' => 'varchar',
        'input' => 'select',
    ];

    protected $products_count = [
        'attribute_code' => 'products_count',
        'backend_type' => 'varchar',
    ];

    protected $cache_lifetime = [
        'attribute_code' => 'cache_lifetime',
        'backend_type' => 'varchar',
    ];

    protected $page_size = [
        'attribute_code' => 'page_size',
        'backend_type' => 'varchar',
    ];

    protected $store_ids = [
        'attribute_code' => 'store_ids',
        'backend_type' => 'virtual',
        'source' => 'Magento\Widget\Test\Fixture\Widget\StoreIds',
        'group' => 'frontend_properties',
    ];

    protected $widget_instance = [
        'attribute_code' => 'widget_instance',
        'backend_type' => 'virtual',
    ];

    protected $parameters = [
        'attribute_code' => 'parameters',
        'backend_type' => 'virtual',
    ];

    protected $id = [
        'attribute_code' => 'id',
        'backend_type' => 'virtual',
    ];

    protected $page_id = [
        'attribute_code' => 'page_id',
        'backend_type' => 'virtual',
        'source' => 'Magento\Widget\Test\Fixture\Widget\PageIds',
    ];

    protected $layout = [
        'attribute_code' => 'layout',
        'backend_type' => 'virtual',
        'source' => 'Magento\Widget\Test\Fixture\Widget\LayoutUpdates',
        'group' => 'layout_updates',
    ];

    protected $widgetOptions = [
        'attribute_code' => 'widgetOptions',
        'backend_type' => 'virtual',
        'source' => 'Magento\Widget\Test\Fixture\Widget\WidgetOptions',
        'group' => 'widget_options',
    ];

    public function getAnchorText()
    {
        return $this->getData('anchor_text');
    }

    public function getTitle()
    {
        return $this->getData('title');
    }

    public function getTemplate()
    {
        return $this->getData('template');
    }

    public function getChosenOption()
    {
        return $this->getData('chosen_option');
    }

    public function getDisplayType()
    {
        return $this->getData('display_type');
    }

    public function getShowPager()
    {
        return $this->getData('show_pager');
    }

    public function getProductsCount()
    {
        return $this->getData('products_count');
    }

    public function getCacheLifetime()
    {
        return $this->getData('cache_lifetime');
    }

    public function getPageSize()
    {
        return $this->getData('page_size');
    }

    public function getCode()
    {
        return $this->getData('code');
    }

    public function getThemeId()
    {
        return $this->getData('theme_id');
    }

    public function getStoreIds()
    {
        return $this->getData('store_ids');
    }

    public function getWidgetInstance()
    {
        return $this->getData('widget_instance');
    }

    public function getParameters()
    {
        return $this->getData('parameters');
    }

    public function getId()
    {
        return $this->getData('id');
    }

    public function getPageId()
    {
        return $this->getData('page_id');
    }

    public function getLayout()
    {
        return $this->getData('layout');
    }

    public function getWidgetOptions()
    {
        return $this->getData('widgetOptions');
    }
}
