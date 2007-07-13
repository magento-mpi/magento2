<?php
class Mage_Adminhtml_Block_Tax_Rate_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tax/rate_collection')->addAttributes();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('region_name',
            array(
                'header'=>__('State'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'region_name'
            )
        );

        $this->addColumn('county_name',
            array(
                'header'=>__('County'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'county_name',
                'default' => __('Any')
            )
        );

        $this->addColumn('zip_code',
            array(
                'header'=>__('Zip code'),
                'align' =>'left',
                'filter'    =>false,
                'index' => 'tax_zip_code',
                'default' => __('Any')
            )
        );

        $rateTypes = Mage::getResourceModel('tax/rate_type_collection')->load()->getItems();

        $index = 0;
        foreach( $rateTypes as $type ) {
            $this->addColumn("tax_type_{$index}",
                array(
                    'header'=>$type->getTypeName(),
                    'align' =>'left',
                    'filter'    =>false,
                    'index' => "rate_value{$index}",
                    'default' => __('N/A')
                )
            );
            $index++;
        }

       $actionsUrl = Mage::getUrl('*/*/');
       $this->addColumn('grid_actions',
            array(
                'header'=>__('Actions'),
                'width'=>10,
                'sortable'=>false,
                'filter'    =>false,
                'type' => 'action',
                'actions'   => array(
                                    array(
                                        'url' => $actionsUrl .'edit/rate/$tax_rate_id',
                                        'caption' => __('Edit')
                                    ),

                                    array(
                                        'url' => $actionsUrl .'delete/rate/$tax_rate_id',
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