<?php
/**
 * Websites collection
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Mysql4_Website_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
	protected $_loadDefault = false;
    
    protected function _construct() 
    {
        $this->_init('core/website');
    }
    
    public function setLoadDefault($loadDefault)
    {
    	$this->_loadDefault = $loadDefault;
    	return $this;
    }
    
    public function getLoadDefault()
    {
    	return $this->_loadDefault;
    }
    
    public function toOptionArray()
    {
        return $this->_toOptionArray('website_id', 'name');
    }
    
    public function load($printQuery = false, $logQuery = false)
    {
    	if (!$this->getLoadDefault()) {
    		$this->getSelect()->where($this->getConnection()->quoteInto('main_table.website_id>?', 0));
    	}
    	parent::load($printQuery, $logQuery);
    	return $this;
    }
}