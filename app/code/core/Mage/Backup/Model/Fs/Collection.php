<?
/**
 * Backup data collection
 *
 * @category   Mage
 * @package    Mage_Backup
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Backup_Model_Fs_Collection extends Varien_Data_Collection
{
    /**
     * Is loaded data flag
     * @var boolean
     */
    protected $_isLoaded = false;
    
        
    /**
     * Constructor
     * 
     * Sets default item object class and default sort order.
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
    public function getSize()
    {        
        $this->_loadFiles();
        return $this->_totalRecords;
    }
    
    public function load($printQuery = false, $logQuery = false)
    {
    	$this->_loadFiles();
    	
    	if($this->getPageSize()) {
    		$this->_items = array_slice($this->_items, ($this->getCurPage()-1)*$this->getPageSize(), $this->getPageSize());
    	}
    	    	    	
    	return $this;
    }
        
    protected function _loadFiles()
    {
    	if(!$this->_isLoaded) {
    		
    		$readPath = Mage::getBaseDir("var") . DS . "backups";
	        
	        $ioProxy = new Varien_Io_File();
	        
	        try {
	            $ioProxy->open(array('path'=>$readPath));
	        }
	        catch (Exception $e) {
	            $ioProxy->mkdir($readPath, 0755);
	            $ioProxy->chmod($readPath, 0755);
	            $ioProxy->open(array('path'=>$readPath));
	        }
	                
	        if (!is_file($readPath . DS . ".htaccess")) {
	            // Deny from reading in browser
	            $ioProxy->write(".htaccess","deny from all", 0644);            
	        }
	        
	        
	        $list = $ioProxy->ls(Varien_Io_File::GREP_FILES);
	        
	        $fileExtension = constant($this->_itemObjectClass . "::BACKUP_EXTENSION"); 
	        
	        foreach ($list as $entry) {
	            if ($entry['filetype'] == $fileExtension) {
	                $item = new $this->_itemObjectClass();
	                $item->load($entry['text'], $readPath);
	                if($this->_checkCondition($item)) {
	                	$this->addItem($item);
	                }
	            }
	        }
	                    
	        
	        $this->_totalRecords = count($this->_items);
	               
	        if ($this->_totalRecords > 1) {
	            usort($this->_items, array(&$this, 'compareByTypeOrDate'));
	        }
	        
	        $this->_isLoaded = true;
    	}
    	
    	return $this;
    }
    
    /**
     * Set sort order for items
     *
     * @param   string $field
     * @param   string $direction
     * @return  Mage_Backup_Model_Fs_Collection
     */
    public function setOrder($field, $direction = 'desc')
    {
        $direction = (strtoupper($direction)=='ASC') ? 1 : -1;
        $this->_orders = array($field, $direction);
        return $this;
    }
    
    /**
     * Function for comparing two items in collection
     *
     * @param   Varien_Object $item1
     * @param   Varien_Object $item2
     * @return  boolean
     */
    public function compareByTypeOrDate(Varien_Object $item1,Varien_Object $item2) 
    {
        if (is_string($item1->getData($this->_orders[0]))) {
            return strcmp($item1->getData($this->_orders[0]),$item2->getData($this->_orders[0]))*(-1*$this->_orders[1]);
        } else if ($item1->getData($this->_orders[0]) < $item2->getData($this->_orders[0])) {
            return 1*(-1*$this->_orders[1]);
        } else if ($item1->getData($this->_orders[0]) > $item2->getData($this->_orders[0])) {
            return -1*(-1*$this->_orders[1]);
        } else {
            return 0;
        }
    }
    
    public function addFieldToFilter($fieldName, $condition)
    {
    	$this->_filters[$fieldName] = $condition;
    	return $this;
    }
    
    protected function _checkCondition($item)
    {
    	
    	foreach ($this->_filters as $field=>$condition) {
	    	if (is_array($condition)) {
	            if (isset($condition['from']) || isset($condition['to'])) {
	            	if($field == 'time_formated') {
	            		if(isset($condition['from'])) {
	            			$condition['from'] = strtotime($condition['from']);
	            		} 
	            		if(isset($condition['to'])) {
	            			$condition['to'] = strtotime($condition['to'])+24*60*60-1;
	            		} 
	            		$field = 'time';
	            	}
	            		            	
	                if (isset($condition['from']) && $item->getData($field) < $condition['from']) {
                    	return false;
	                }
	                if (isset($condition['to']) && $item->getData($field) > $condition['to']) {
	 					return false;
	                }
	            }
	            elseif (!empty($condition['neq']) && $item->getData($field) == $condition['neq']) {
	                return false;
	            }
	            elseif (!empty($condition['like']) && strpos($item->getData($field), trim($condition['like'], '%')) === false) {
	                return false;
	            }
	            elseif (!empty($condition['nlike']) && strpos($item->getData($field), trim($condition['nlike'], '%')) !== false) {
	                return false;
	            }
	            elseif (!empty($condition['in'])) {
	                $values = $condition['in'];
	                if(!is_array($values)) {
	                	$values =  array($values);
	                }	                
	                if(!in_array($item->getData($field), $values)) {
	                	return false;
	                }
	            }
	            elseif (!empty($condition['nin'])) {
	                $values = $condition['in'];
	                if(!is_array($values)) {
	                	$values =  array($values);
	                }	                
	                if(in_array($item->getData($field), $values)) {
	                	return false;
	                }
	            }
	        } else if($item->getData($field) != $condition) {
	            return false;
	        }
    	}
    	
        return true;
    }
}