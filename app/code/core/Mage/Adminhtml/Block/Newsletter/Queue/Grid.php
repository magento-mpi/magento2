<?php
/**
 * Adminhtml newsletter queue grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Newsletter_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('queueGrid');
        $this->setDefaultSort('id');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('newsletter/queue_collection');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    =>__('ID'),
            'align'     =>'center',
            'sortable'  =>true,
            'index'     =>'queue_id'
        ));
        
        $this->addColumn('start_at', array(
            'header'    =>__('Queue start'),
            'type'      => 'date',
            'align'     => 'center',
            'index'     =>'queue_start_at',
        ));
        
        $this->addColumn('finish_at', array(
            'header'    =>__('Queue finish'),
            'type'      => 'date',
            'align'     => 'center',
            'index'     =>'queue_finish_at',
        ));
        
        return parent::_prepareColumns();
    }

}