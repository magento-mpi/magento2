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
 * Search query relations edit grid
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Block_Adminhtml_Search_Grid extends Magento_Backend_Block_Widget_Grid
{
    /**
     * @var Enterprise_Search_Model_Adminhtml_Search_Grid_Options
     */
    protected $_options;

    /**
     * @var Magento_Core_Model_Registry
     */
    protected $_registryManager;

    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Enterprise_Search_Model_Adminhtml_Search_Grid_Options $options,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        parent::__construct($context, $storeManager, $urlModel, $data);
        $this->_options = $options;
        $this->_registryManager = $registry;
        $this->setDefaultFilter(array('query_id_selected' => 1));
    }

    /**
     *  Retrieve a value from registry by a key
     *
     * @return mixed
     */
    public function getQuery()
    {
        return $this->_registryManager->registry('current_catalog_search');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for query selected flag
        if ($column->getId() == 'query_id_selected' && $this->getQuery()->getId()) {
            $selectedIds = $this->getSelectedQueries();
            if (empty($selectedIds)) {
                $selectedIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('query_id', array('in' => $selectedIds));
            } elseif (!empty($selectedIds)) {
                $this->getCollection()->addFieldToFilter('query_id', array('nin' => $selectedIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Retrieve selected related queries from grid
     *
     * @return array
     */
    public function getSelectedQueries()
    {
        return $this->_options->toOptionArray();
    }

    /**
     * @return string
     */
    public function getQueriesJson()
    {
        $queries = array_flip($this->getSelectedQueries());
        if (!empty($queries)) {
            return $this->helper('Magento_Core_Helper_Data')->jsonEncode($queries);
        }
        return '{}';
    }
}
