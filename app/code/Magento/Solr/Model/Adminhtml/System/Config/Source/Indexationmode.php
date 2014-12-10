<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Adminhtml\System\Config\Source;

/**
 * Search engine indexation modes
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Indexationmode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $modes = [
            \Magento\Solr\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL => __('Final commit'),
            \Magento\Solr\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_PARTIAL => __(
                'Partial commit'
            ),
            \Magento\Solr\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_ENGINE => __(
                'Engine autocommit'
            ),
        ];

        $options = [];
        foreach ($modes as $value => $label) {
            $options[] = ['value' => $value, 'label' => $label];
        }

        return $options;
    }
}
