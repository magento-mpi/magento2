<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\Catalog\Model\Resource\Setup */

$this->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'category_ids',
    'frontend_input_renderer',
    'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category'
);

$this->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'image',
    'frontend_input_renderer',
    'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\BaseImage'
);

$this->updateAttribute(
    \Magento\Catalog\Model\Product::ENTITY,
    'weight',
    'frontend_input_renderer',
    'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight'
);

$this->updateAttribute(
    \Magento\Catalog\Model\Category::ENTITY,
    'available_sort_by',
    'frontend_input_renderer',
    'Magento\Catalog\Block\Adminhtml\Category\Helper\Sortby\Available'
);

$this->updateAttribute(
    \Magento\Catalog\Model\Category::ENTITY,
    'default_sort_by',
    'frontend_input_renderer',
    'Magento\Catalog\Block\Adminhtml\Category\Helper\Sortby\DefaultSortby'
);

$this->updateAttribute(
    \Magento\Catalog\Model\Category::ENTITY,
    'filter_price_range',
    'frontend_input_renderer',
    'Magento\Catalog\Block\Adminhtml\Category\Helper\Pricestep'
);
