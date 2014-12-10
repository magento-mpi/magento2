<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Adminhtml\System\Config\Source;

/**
 * Catalog search types
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Engine implements \Magento\Framework\Option\ArrayInterface
{
    const FULLTEXT = 'Magento\CatalogSearch\Model\Resource\Engine';

    const SOLR = 'Magento\Solr\Model\Resource\Solr\Engine';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $engines = [self::FULLTEXT => __('MySql Fulltext'), self::SOLR => __('Solr')];
        $options = [];
        foreach ($engines as $k => $v) {
            $options[] = ['value' => $k, 'label' => $v];
        }
        return $options;
    }
}
