<?php
/**
 * Catalog search types
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogSearch\Model\Config\Source\Search;

class Type implements \Magento\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $types = array(
            \Magento\CatalogSearch\Model\Fulltext::SEARCH_TYPE_LIKE     => 'Like',
            \Magento\CatalogSearch\Model\Fulltext::SEARCH_TYPE_FULLTEXT => 'Fulltext',
            \Magento\CatalogSearch\Model\Fulltext::SEARCH_TYPE_COMBINE  => 'Combine (Like and Fulltext)',
        );
        $options = array();
        foreach ($types as $k => $v) {
            $options[] = array(
                'value' => $k,
                'label' => $v
            );
        }
        return $options;
    }
}
