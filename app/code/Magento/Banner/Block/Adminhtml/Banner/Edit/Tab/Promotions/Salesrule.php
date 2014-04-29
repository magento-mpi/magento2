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

use Magento\Backend\Block\Widget\Grid\Column;

class Salesrule extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\SalesRule\Model\Resource\Rule\Collection $ruleCollection
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\SalesRule\Model\Resource\Rule\Collection $ruleCollection,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $backendHelper, $data);
        $this->setCollection($ruleCollection);
    }

    /**
     * Initialize grid, set defaults
     *
     * @return void
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
     * @param Column $column
     * @return $this
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
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_banner_salesrule',
            array(
                'type' => 'checkbox',
                'name' => 'in_banner_salesrule',
                'values' => $this->_getSelectedRules(),
                'index' => 'rule_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            )
        );
        $this->addColumn(
            'salesrule_rule_id',
            array(
                'header' => __('ID'),
                'index' => 'rule_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );

        $this->addColumn(
            'salesrule_name',
            array(
                'header' => __('Rule'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            )
        );

        $this->addColumn(
            'salesrule_from_date',
            array(
                'header' => __('Start on'),
                'type' => 'date',
                'index' => 'from_date',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            )
        );

        $this->addColumn(
            'salesrule_to_date',
            array(
                'header' => __('End on'),
                'type' => 'date',
                'default' => '--',
                'index' => 'to_date',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            )
        );

        $this->addColumn(
            'salesrule_is_active',
            array(
                'header' => __('Status'),
                'index' => 'is_active',
                'type' => 'options',
                'options' => array(1 => 'Active', 0 => 'Inactive'),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * Ajax grid URL getter
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/*/salesRuleGrid', array('_current' => true));
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
