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
    private $_gzInstalled = false;

    public function __construct()
    {
        $this->_gzInstalled = extension_loaded('zlib');
        parent::__construct();
    }

    /**
     * Init backups collection
     * @return void
     */
    protected function _initCollection()
    {
        $collection = Mage::getSingleton('backup/fs_collection');
        $this->setCollection($collection);
    }

    /**
     * Configuration of grid
     */
    protected function _beforeToHtml()
    {
        $gridUrl = Mage::getUrl('*/*/');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(true);
        $this->addColumn('time', array(
                                'header'=>__('Time'),
                                'align'=>'center',
                                'index'=>'time_formated',
                                'type' => 'datetime')
                                );
        $this->addColumn('type', array(
                                'header'=>__('Type'),
                                'align'=>'center',
                                'filter'    => 'adminhtml/backup_grid_filter_type',
                                'index'=>'type')
                                );
        $this->addColumn('download', array('header'=>__('Download'),'align'=>'center',
                                           'format'=>'<a href="' . $gridUrl .'download/time/$time/type/$type/file/sql/">sql</a><span class="separator">&bull;</span><a href="' . $gridUrl .'download/time/$time/type/$type/file/gz/">gz</a>',
                                           'index'=>'type', 'sortable'=>false, 'filter' => false));
        $this->addColumn('action', array(
                                'header'=>__('Action'),
                                'align'=>'center',
                                'type' => 'action',
                                'width' => '80px',
                                'filter' => false,
                                'actions' => array(
                                    array(
                                        'url' => $gridUrl .'delete/time/$time/type/$type/',
                                        'caption' => __('Delete'),
                                        'confirm' => __('Are you sure you want to do this?')
                                    )
                                ),
                                'index'=>'type', 'sortable'=>false));
        $this->_initCollection();
        return parent::_beforeToHtml();
    }

}
