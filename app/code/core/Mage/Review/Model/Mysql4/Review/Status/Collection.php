<?php
/**
 * Review sttuses collection
 *
 * @package     Mage
 * @subpackage  Review
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Review_Model_Mysql4_Review_Status_Collection extends Varien_Data_Collection_Db
{
    protected $_reviewStatusTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('review_read'));

        $this->_reviewStatusTable = Mage::getSingleton('core/resource')->getTableName('review/review_status');

        $this->_sqlSelect->from($this->_reviewStatusTable);
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('status_id', 'status_code');
    }
}