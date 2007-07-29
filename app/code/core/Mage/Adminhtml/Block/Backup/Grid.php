<?php
/**
 * Adminhtml backups grid block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_Block_Backup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    
    protected function _construct()
    {
        $this->setUseAjax(true);
        $this->setId('backupsGrid');
		$this->setDefaultSort('time', 'desc');       
    }

    /**
     * Init backups collection
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getSingleton('backup/fs_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $gridUrl = Mage::getUrl('*/*/');
        
        $this->addColumn('time', array(
                                'header'=>__('Time'),
                                'index'=>'time_formated',
                                'type' => 'datetime')
                                );
        $this->addColumn('type', array(
                                'header'=>__('Type'),
                                'filter'    => 'adminhtml/backup_grid_filter_type',
                                'renderer'    => 'adminhtml/backup_grid_renderer_type',
                                'index'=>'type')
                                );
        $this->addColumn('download', array('header'=>__('Download'),
                                           'format'=>'<a href="' . $gridUrl .'download/time/$time/type/$type/file/sql/">sql</a><span class="separator">&nbsp;|&nbsp;</span><a href="' . $gridUrl .'download/time/$time/type/$type/file/gz/">gz</a>',
                                           'index'=>'type', 'sortable'=>false, 'filter' => false));
        $this->addColumn('action', array(
                                'header'=>__('Action'),
                                'type' => 'action',
                                'width' => '80px',
                                'filter' => false,
                                'sortable' => false,
                                'actions' => array(
                                    array(
                                        'url' => $gridUrl .'delete/time/$time/type/$type/',
                                        'caption' => __('Delete'),
                                        'confirm' => __('Are you sure you want to do this?')
                                    )
                                ),
                                'index'=>'type', 'sortable'=>false));
        return $this;
    }

}
