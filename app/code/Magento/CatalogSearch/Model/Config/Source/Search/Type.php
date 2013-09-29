<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search types
 *
 * @category   Magento
 * @package    Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogSearch\Model\Config\Source\Search;

class Type implements \Magento\Core\Model\Option\ArrayInterface
{
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
