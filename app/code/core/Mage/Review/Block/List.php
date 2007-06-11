<?php
/**
 * Review list block
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Review_Block_List extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        $this->setTemplate('review/list.phtml');
    }
}
