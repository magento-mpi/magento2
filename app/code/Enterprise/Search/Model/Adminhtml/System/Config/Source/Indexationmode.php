<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Search engine indexation modes
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Adminhtml_System_Config_Source_Indexationmode
{
    /**
     * Prepare options for selection
     *
     * @return array
     */
    public function toOptionArray()
    {
        $modes = array(
            Enterprise_Search_Model_Indexer_Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_FINAL    =>
                __('Final commit'),
            Enterprise_Search_Model_Indexer_Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_PARTIAL  =>
                __('Partial commit'),
            Enterprise_Search_Model_Indexer_Indexer::SEARCH_ENGINE_INDEXATION_COMMIT_MODE_ENGINE   =>
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
