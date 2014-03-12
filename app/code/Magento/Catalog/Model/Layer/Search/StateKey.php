<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Search;


class StateKey extends \Magento\Catalog\Model\Layer\Category\StateKey
{
    /**
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\CatalogSearch\Helper\Data $helper
     */
    public function __construct(\Magento\CatalogSearch\Helper\Data $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param $category
     * @return string|void
     */
    public function toString($category)
    {
        return 'Q_' . $this->helper->getQuery()->getId() . '_' . parent::toString($category);
    }
} 
