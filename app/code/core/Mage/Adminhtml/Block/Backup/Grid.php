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
        $this->setFilterVisibility(false);
        $this->addColumn('time', array('header'=>__('time'), 'align'=>'center', 'index'=>'time_formated'));
        $this->addColumn('type', array('header'=>__('type'),'align'=>'center', 'index'=>'type'));
        $this->addColumn('download', array('header'=>__('download'),'align'=>'center',
                                           'format'=>'<a href="' . $gridUrl .'download/time/$time/type/$type/file/sql/">sql</a> | <a href="' . $gridUrl .'download/time/$time/type/$type/file/gz/">gz</a>',
                                           'index'=>'type', 'sortable'=>false));
        $this->addColumn('action', array('header'=>__('action'),'align'=>'center',
                                         'format'=>'<a href="' . $gridUrl .'delete/time/$time/type/$type/">' . __('delete') . '</a>',
                                         'index'=>'type', 'sortable'=>false));
        $this->_initCollection();
        return parent::_beforeToHtml();
    }
    
}