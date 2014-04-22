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

class Catalogrule extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_registry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\CatalogRule\Model\Resource\Rule\Collection $ruleCollection
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\CatalogRule\Model\Resource\Rule\Collection $ruleCollection,
        \Magento\Registry $registry,
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
        $this->setId('related_catalogrule_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('related_catalogrule_filter');
        if ($this->_getBanner() && $this->_getBanner()->getId()) {
            $this->setDefaultFilter(array('in_banner_catalogrule' => 1));
        }
    }

    /**
     * Set custom filter for in banner catalog flag
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_banner_catalogrule') {
            $ruleIds = $this->_getSelectedRules();
            if (empty($ruleIds)) {
                $ruleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('rule_id', array('in' => $ruleIds));
            } else {
                if ($ruleIds) {
                    $this->getCollection()->addFieldToFilter('rule_id', array('nin' => $ruleIds));
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
            'in_banner_catalogrule',
            array(
                'type' => 'checkbox',
                'name' => 'in_banner_catalogrule',
                'values' => $this->_getSelectedRules(),
                'index' => 'rule_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            )
        );
        $this->addColumn(
            'catalogrule_rule_id',
            array(
                'header' => __('ID'),
                'index' => 'rule_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            )
        );

        $this->addColumn(
            'catalogrule_name',
            array(
                'header' => __('Rule'),
                'index' => 'name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            )
        );

        $this->addColumn(
            'catalogrule_from_date',
            array(
                'header' => __('Start on'),
                'type' => 'date',
                'index' => 'from_date',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            )
        );

        $this->addColumn(
            'catalogrule_to_date',
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
            'catalogrule_is_active',
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
        return $this->getUrl('adminhtml/*/catalogRuleGrid', array('_current' => true));
    }

    /**
     * @return array
     */
    protected function _getSelectedRules()
    {
        $rules = $this->getSelectedCatalogRules();
        if (is_null($rules)) {
            $rules = $this->getRelatedCatalogRule();
        }
        return $rules;
    }

    /**
     * Get related sales rules by current banner
     *
     * @return array
     */
    public function getRelatedCatalogRule()
    {
        return $this->_getBanner()->getRelatedCatalogRule();
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
