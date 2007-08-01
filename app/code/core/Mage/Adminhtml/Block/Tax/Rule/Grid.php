<?php
class Mage_Adminhtml_Block_Tax_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('tax_rule_id');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tax/rule_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('tax_rule_id',
            array(
                'header'=>__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'tax_rule_id'
            )
        );

        $this->addColumn('customer_class_name',
            array(
                'header'=>__('Customer Tax Class'),
                'align' =>'left',
                'index' => 'customer_class',
                'filter_index' => 'cct.class_name',
            )
        );

        $this->addColumn('product_class_name',
            array(
                'header'=>__('Product Tax Class'),
                'align' =>'left',
                'index' => 'product_class',
                'filter_index' => 'pct.class_name',
            )
        );

        $this->addColumn('type_name',
            array(
                'header'=>__('Tax Rate'),
                'align' =>'left',
                'index' => 'type_name'
            )
        );

        $actionsUrl = Mage::getUrl('*/*/');

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return Mage::getUrl('*/*/edit', array('rule' => $row->getTaxRuleId()));
    }
}
