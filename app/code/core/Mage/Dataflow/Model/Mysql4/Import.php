<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * DataFlow Import resource model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author     Vincent Maung <vincent@varien>
 */
class Mage_Dataflow_Model_Mysql4_Import extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('dataflow/import', 'import_id');
    }

    public function loadBySessionId($session_id, $start = 0)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('dataflow/import'), '*')
            ->where('status=?', '0')
            ->where('session_id=?', $session_id)
            ->limit($start, 100);
        return $read->fetchAll($select);
    }

    public function loadTotalBySessionId($session_id)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('dataflow/import'),
        array('max'=>'max(import_id)','min'=>'min(import_id)', 'cnt'=>'count(*)'))
            ->where('status=?', '0')
            ->where('session_id=?', $session_id);
        return $read->fetchRow($select);
    }

    public function loadById($import_id)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()->from($this->getTable('dataflow/import'),'*')
            ->where('status=?', 0)
            ->where('import_id=?', $import_id);
        return $read->fetchRow($select);
    }

}
