<?php
/**
 * Rating model
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Rating_Model_Mysql4_Rating extends Mage_Core_Model_Mysql4_Abstract
{
    public function __construct()
    {
        $this->_init('rating/rating', 'rating_id');
    }
}