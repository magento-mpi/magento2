<?php
/**
 * Newsletter queue collection.
 * 
 * @package     Mage
 * @subpackage  Newsletter
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */ 
class Mage_Newsletter_Model_Mysql4_Queue_Collection extends Mage_Core_Model_Resource_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('newsletter/queue');
    }
}
