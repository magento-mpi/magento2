<?php
/**
 * Review summary resource model
 *
 * @package     Mage
 * @subpackage  Raview
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Review_Model_Mysql4_Review_Summary extends Mage_Core_Model_Mysql4_Abstract
{
    public function __construct()
    {
        $this->_init('review/review_aggregate', 'entity_pk_value');
    }
}