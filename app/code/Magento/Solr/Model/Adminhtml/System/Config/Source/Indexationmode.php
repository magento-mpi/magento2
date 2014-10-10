<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $modes = array(
            \Magento\Solr\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL => __('Final commit'),
            \Magento\Solr\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_PARTIAL => __(
                'Partial commit'
            ),
            \Magento\Solr\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_ENGINE => __(
                'Engine autocommit'
            )
        );

        $options = array();
        foreach ($modes as $value => $label) {
            $options[] = array('value' => $value, 'label' => $label);
        }

        return $options;
    }
}
