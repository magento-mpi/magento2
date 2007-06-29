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
        $collection->load();
        $this->setCollection($collection);
    }

    /**
     * Configuration of grid
     */
    protected function _beforeToHtml()
    {
        $this->addColumn('time', array('header'=>__('time'), 'align'=>'center', 'index'=>'time_formated'));
        $this->addColumn('type', array('header'=>__('type'),'align'=>'center', 'index'=>'type'));
        $this->addColumn('download', array('header'=>__('download'),'align'=>'center', 'type'=> 'action',  'index'=>'type', 'sortable'=>false));
        $this->addColumn('action', array('header'=>__('action'),'align'=>'center', 'type'=> 'action', 'index'=>'type', 'sortable'=>false));
        $this->_initCollection();
        return parent::_beforeToHtml();
    }
    
    /**
     * Custom item renderer
     *
     * @param   Varien_Object $row
     * @param   Varien_Object $column
     * @return  string
     */     
    public function getRowField(Varien_Object $row, Varien_Object $column)
    {
        if ($column->getType() != 'action') {
            return $row->getData($column->getIndex());
        } else {
            $html = '';
            
            switch($column->getId())
            {
                case "download":
                    $html = $this->getDownloadHTML($row);
                    break;
                case "action":
                    $html = $this->getActionsHTML($row);
                    break;
                default:
                    $html = $this->getActionsHTML($row);
                    break;
            }
            
            return $html;
        }
    }
    
    /**
     * Return download action HTML for current row
     *
     * @param   Varien_Object $row
     * @return  string
     */
    protected function getDownloadHTML(Varien_Object $row) 
    {
        return '<a href="' . Mage::getUrl('adminhtml',array('controller'    =>  'backup',
                                                            'action'        =>  'download',
                                                            'time'          =>  $row->getTime(),
                                                            'type'          =>  $row->getType(),
                                                            'file'          =>  'sql'))
             . '">sql</a> ' 
             . ( $this->_gzInstalled ? '| <a href="'
             . Mage::getUrl('adminhtml',array('controller'    =>  'backup',
                                              'action'        =>  'download',
                                              'time'          =>  $row->getTime(),
                                              'type'          =>  $row->getType(),
                                              'file'          =>  'gz')) . '">gzip</a>' : '' );
    }
    
    /**
     * Return action HTML for current row
     *
     * @param   Varien_Object $row
     * @return  string
     */
    protected function getActionsHTML(Varien_Object $row) 
    {
        return '<a href="' . Mage::getUrl('adminhtml',array('controller'    =>  'backup',
                                                            'action'        =>  'delete',
                                                            'time'          =>  $row->getTime(),
                                                            'type'          =>  $row->getType()))
             . '">' . __('delete') . '</a> ';
    }
}