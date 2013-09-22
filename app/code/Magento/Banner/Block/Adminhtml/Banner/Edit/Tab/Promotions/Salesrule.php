<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions;

class Salesrule extends \Magento\Adminhtml\Block\Widget\Grid
{
    /**
     * @param \Magento\Core\Helper\Data $coreData
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_registry;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_SalesRule_Model_Resource_Rule_Collection $ruleCollection
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_SalesRule_Model_Resource_Rule_Collection $ruleCollection,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
        $this->setCollection($ruleCollection);
    }

    /**
     * Initialize grid, set defaults
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('related_salesrule_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('related_salesrule_filter');
        if ($this->_getBanner() && $this->_getBanner()->getId()) {
            $this->setDefaultFilter(array('in_banner_salesrule' => 1));
        }
    }

    /**
     * Set custom filter for in banner salesrule flag
     *
     * @param string $column
     * @return \Magento\Banner\Block\Adminhtml\Banner\Edit\Tab\Promotions\Salesrule
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_banner_salesrule') {
            $ruleIds = $this->_getSelectedRules();
            if (empty($ruleIds)) {
                $ruleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.rule_id', array('in' => $ruleIds));
            } else {
                if ($ruleIds) {
                    $this->getCollection()->addFieldToFilter('main_table.rule_id', array('nin' => $ruleIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Create grid columns
     *
     * @return \Magento\Adminhtml\Block\Widget\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_banner_salesrule', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_banner_salesrule',
            'values'    => $this->_getSelectedRules(),
            'align'     => 'center',
            'index'     => 'rule_id'
        ));
        $this->addColumn('salesrule_rule_id', array(
            'header'    => __('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('salesrule_name', array(
            'header'    => __('Rule'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('salesrule_from_date', array(
            'header'    => __('Start on'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('salesrule_to_date', array(
            'header'    => __('End on'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('salesrule_is_active', array(
            'header'    => __('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Ajax grid URL getter
     *
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/salesRuleGrid', array('_current' => true));
    }

    /**
     * Get selected rules ids for in banner salesrule flag
     *
     * @return array
     */
    protected function _getSelectedRules()
    {
        $rules = $this->getSelectedSalesRules();
        if (is_null($rules)) {
            $rules = $this->getRelatedSalesRule();
        }
        return $rules;
    }

    /**
     * Get related sales rules by current banner
     *
     * @return array
     */
    public function getRelatedSalesRule()
    {
        return $this->_getBanner()->getRelatedSalesRule();
    }

    /**
     * Get current banner model
     *
     * @return \Magento\Banner\Model\Banner
     */
    protected function _getBanner()
    {
        return $this->_registry->registry('current_banner');
    }
}
