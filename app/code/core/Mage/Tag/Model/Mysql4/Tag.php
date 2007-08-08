<?php
/**
 * Tag model
 *
 * @package    Mage
 * @subpackage Tag
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Tag_Model_Mysql4_Tag extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('tag/tag', 'tag_id');
    }

}
