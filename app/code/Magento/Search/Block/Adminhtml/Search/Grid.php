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
 * Search query relations edit grid
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Search\Block\Adminhtml\Search;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * @var \Magento\Search\Model\Adminhtml\Search\Grid\Options
     */
    protected $_options;

    /**
     * @var \Magento\Core\Model\Registry
     */
    protected $_registryManager;

    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Url $urlModel,
        \Magento\Search\Model\Adminhtml\Search\Grid\Options $options,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
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
            return $this->helper('Magento\Core\Helper\Data')->jsonEncode($queries);
        }
        return '{}';
    }
}
