<?php
/**
 * Translation resource model
 *
 * @package     Mage
 * @subpackage  Core
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_Mysql4_Translate extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('core/translate', 'key_id');
    }
    
    public function getTranslationArray($storeId=null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::getSingleton('core/store')->getId();
        }
        
        $read = $this->getConnection('read');
        $select = $read->select()
            ->from(array('main'=>$this->getMainTable()), array(
                    'string',
                    new Zend_Db_Expr('IFNULL(store.translate, main.translate)')
                ))
            ->joinLeft(array('store'=>$this->getMainTable()), 
                $read->quoteInto('store.string=main.string AND store.store_id=?', $storeId), 
                'string')
            ->where('main.store_id=0');
        return $read->fetchPairs($select);
    }
}
