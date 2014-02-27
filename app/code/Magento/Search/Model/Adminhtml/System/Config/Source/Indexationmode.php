<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model\Adminhtml\System\Config\Source;

/**
 * Search engine indexation modes
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Indexationmode implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $modes = array(
            \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL    =>
                __('Final commit'),
            \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_PARTIAL  =>
                __('Partial commit'),
            \Magento\Search\Model\Indexer\Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_ENGINE   =>
                __('Engine autocommit')
        );

        $options = array();
        foreach ($modes as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label
            );
        }

        return $options;
    }
}
