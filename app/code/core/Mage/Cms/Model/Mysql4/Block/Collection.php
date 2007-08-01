<?php
/**
 * CMS block model
 *
 * @package     Mage
 * @subpackage  Cms
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Cms_Model_Mysql4_Block_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('cms/block');
    }
    
    public function toOptionArray()
    {
        return $this->_toOptionArray('block_id', 'title');
    }
}
