<?php
/**
 * Adminhtml cms grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Cms_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('cmsGrid');
        $this->setDefaultSort('page_identifier');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $pageCollection = Mage::getResourceModel('cms/page_collection');
        $this->setCollection($pageCollection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $baseUrl = Mage::getUrl();

        $this->addColumn('page_title',
            array(
                'header'=>__('Title'),
                'align' =>'left',
                'format'=> '<a href="'.$baseUrl.'$page_identifier" target="_blank">$page_title</a>',
                'index' =>'page_title',
            )
        );

        $this->addColumn('page_identifier',
            array(
                'header'=>__('Identifier'),
                'align' =>'left',
                'format'=> '<a href="' . $baseUrl . '$page_identifier" target="_blank">$page_identifier</a>',
                'index' =>'page_identifier'
            )
        );

        $this->addColumn('page_creation_time',
            array(
                'header'=>__('Creation Time'),
                'index' =>'page_creation_time',
                'type' => 'datetime',
            )
        );

        $this->addColumn('page_update_time',
            array(
                'header'=>__('Update Time'),
                'index'=>'page_update_time',
                'type' => 'datetime',
            )
        );

        $this->addColumn('page_is_active',
            array(
                'header'=>__('Enabled'),
                'index'=>'page_active',
                'type' => 'boolean',
                'values' => array(__('Disabled'), __('Enabled'))
            )
        );

        $this->addColumn('page_actions',
            array(
                'header'    =>__('Action'),
                'width'     =>10,
                'sortable'  =>false,
                'filter'    => false,
                'type' => 'action',
                'actions' => array(
                    array(
                        'url' => Mage::getUrl('*/*/edit') .'page/$page_id/',
                        'caption' => __('Edit')
                    ),

                    array(
                        'url' => Mage::getUrl('*/*/delete') .'page/$page_id/',
                        'caption' => __('Delete'),
                        'confirm' => __('Are you sure you want to do this?')
                    ),
                )
            )
        );
        $this->setFilterVisibility(false);

        return parent::_prepareColumns();
    }
}
