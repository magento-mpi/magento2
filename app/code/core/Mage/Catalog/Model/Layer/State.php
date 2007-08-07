<?php
/**
 * State of layered navigation
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Layer_State extends Varien_Object
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function addFilter($filter)
    {
        $filters = $this->getFilters();
        $filters[] = $filter;
        $this->setFilters($filters);
        return $this;
    }
    
    public function setFilters($filters)
    {
        if (!is_array($filters)) {
            Mage::throwException('Filters must be as array');
        }
        $this->setData('filters', $filters);
        return $this;
    }
    
    public function getFilters()
    {
        $filters = $this->getData('filters');
        if (is_null($filters)) {
            $filters = array();
            $this->setData('filters', $filters);
        }
        return $filters;
    }
}
