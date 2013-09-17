<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Search engine indexation modes
 *
 * @category    Magento
 * @package     Magento_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Model_Adminhtml_System_Config_Source_Indexationmode
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Prepare options for selection
     *
     * @return array
     */
    public function toOptionArray()
    {
        $modes = array(
            Magento_Search_Model_Indexer_Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL    =>
                __('Final commit'),
            Magento_Search_Model_Indexer_Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_PARTIAL  =>
                __('Partial commit'),
            Magento_Search_Model_Indexer_Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_ENGINE   =>
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
