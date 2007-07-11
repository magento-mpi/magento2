<?php
/**
 * Region
 *
 * @package    Mage
 * @subpackage Directory
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Directory_Model_Region extends Mage_Core_Model_Abstract 
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
        $this->_init('directory/region');
        $this->setIdFieldName($this->getResource()->getIdFieldName());
    }
}
