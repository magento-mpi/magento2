<?
/**
 * Backup data collection
 *
 * @package    Mage
 * @subpackage Backup
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <alexander@varien.com>
 */
class Mage_Backup_Model_Fs_Collection extends Varien_Data_Collection
{
    /**
     * Constructor
     * 
     * Sets default item object class
     */
    public function __construct() 
    {
        parent::__construct();
        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('backup/backup'))
             ->setOrder('time','desc');
        
    }
    
    /**
     * Loads data from backup directory
     *
     * @return Mage_Backup_Model_Fs_Collection
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        parent::loadData($printQuery, $logQuery);
        
        $readPath = Mage::getBaseDir("var") . DS . "backups";
        if (!is_dir($readPath)) {
            mkdir($readPath, 0755);
            chmod($readPath, 0755);
        }
        
        $dirResource = dir($readPath);
        if (!$dirResource) {
            throw Mage::exception('Mage_Backup', "Couldn't read backups directory");
        }
        
        $fileExtension = constant($this->_itemObjectClass . "::BACKUP_EXTENSION"); 
        
        while ($entity = $dirResource->read()) {
            if (substr($entity, strrpos($entity,".")+1)==$fileExtension) {
                $item = new $this->_itemObjectClass();
                $this -> addItem($item->load($entity, $readPath));
            }
        }
        
        usort($this->_items, array(&$this, 'compareByTypeOrDate'));
        $this->_totalRecords = count($this->_items);
        return $this;
    }
    
    
    public function setOrder($field, $direction = 'desc')
    {
        $direction = (strtoupper($direction)=='ASC') ? 1 : 0;
        $this->_orders = array($field, $direction);
        return $this;
    }
    
    public function compareByTypeOrDate(Varien_Object $item1,Varien_Object $item2) 
    {
        if (is_integer($item1->getData($this->_orders[0])) 
                &&
            $item1->getData($this->_orders[0]) < $item2->getData($this->_orders[0])) {
            return 1*(-1*$this->_orders[1]);
        } else if (is_integer($item1->getData($this->_orders[0])) 
                        && 
                   $item1->getData($this->_orders[0]) > $item2->getData($this->_orders[0])) {
            return -1*(-1*$this->_orders[1]);
        } else if (is_string($item1->getData($this->_orders[0]))) {
            return strcmp($item1->getData($this->_orders[0]),$item2->getData($this->_orders[0]))*(-1*$this->_orders[1]);
        } else {
            return 0;
        }
    }
        
}