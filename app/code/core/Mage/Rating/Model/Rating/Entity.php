<?php
/**
 * Ratings entity model
 *
 * @package     Mage
 * @subpackage  Rating
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Rating_Model_Rating_Entity extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('rating/rating_entity');
    }

    public function getIdByCode($entityCode)
    {
        return $this->getResource()->getIdByCode($entityCode);
    }
}