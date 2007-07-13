<?php
class Mage_Adminhtml_Block_Tax_Rule_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tax/rule_collection');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }


    protected function _prepareColumns()
    {
        $this->addColumn('customer_tax_class',
            array(
                'header'=>__('Customer Tax Class'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'customer_class'
            )
        );

        $this->addColumn('product_tax_class',
            array(
                'header'=>__('Product Tax Class'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'product_class'
            )
        );

        $this->addColumn('tax_rate',
            array(
                'header'=>__('Tax Rate'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'rate_name'
            )
        );

        $actionsUrl = Mage::getUrl('*/*/');

        $this->addColumn('grid_actions',
            array(
                'header'    =>__('Actions'),
                'width'     =>10,
                'filter'    => false,
                'sortable'  => false,
                'type'      => 'action',
                'actions'   => array(
                                    array(
                                        'url' => $actionsUrl .'edit/rule/$tax_rule_id',
                                        'caption' => __('Edit')
                                    ),

                                    array(
                                        'url' => $actionsUrl .'delete/rule/$tax_rule_id',
                                        'caption' => __('Delete'),
                                        'confirm' => __('Are you sure you want to do this?')
                                    )
                                )
            )
        );
        $this->setFilterVisibility(false);
        return parent::_prepareColumns();
    }
}