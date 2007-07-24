<?php
class Mage_Adminhtml_Block_Tax_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setDefaultSort('rule_id');
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

        $this->addColumn('customer_class',
            array(
                'header'=>__('Customer Tax Class'),
                'align' =>'left',
                'index' => 'customer_class'
            )
        );

        $this->addColumn('product_class',
            array(
                'header'=>__('Product Tax Class'),
                'align' =>'left',
                'index' => 'product_class'
            )
        );

        $this->addColumn('tax_rate',
            array(
                'header'=>__('Tax Rate'),
                'align' =>'left',
                'index' => 'rate_name'
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
