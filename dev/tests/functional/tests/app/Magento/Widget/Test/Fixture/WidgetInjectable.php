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
 * Class WidgetInjectable
 */
class WidgetInjectable extends InjectableFixture
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

    protected $anchor_text = [
        'attribute_code' => 'anchor_text',
        'backend_type' => 'varchar',
    ];

    protected $title = [
        'attribute_code' => 'title',
        'backend_type' => 'varchar',
    ];

    protected $template = [
        'attribute_code' => 'template',
        'backend_type' => 'varchar',
        'input' => 'select',
    ];

    protected $chosen_option = [
        'attribute_code' => 'chosen_option',
        'backend_type' => 'virtual',
        'source' => 'Magento\Widget\Test\Fixture\WidgetInjectable\ChosenOption',
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
}
