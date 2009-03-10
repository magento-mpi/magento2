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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Logging_Model_Mysql4_Event_Collection extends  Mage_Core_Model_Mysql4_Collection_Abstract 
{
    private $_ipLoaded = false;

    /**
     * Constructor
     */

    protected function _construct() 
    {
        $this->_init('enterprise_logging/event');
    }

    /**
     * Before load handler
     */
    protected function _initSelect()
    {
        $this->getSelect()
          ->from(array('main_table' => $this->getResource()->getMainTable()))
          ->joinLeft(array('adm' => $this->getTable('admin/user')), 'main_table.user_id=adm.user_id');;
        return $this;
    }

    /**
     * Minimize usual count select
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->resetJoinLeft();
        return $countSelect;
    }

    /**
     * Load method. Joins admin_user table to retrieve username
     */
    public function load($printQuery = false, $logQuery = false) 
    {
        parent::load($printQuery, $logQuery);
        if(!$this->_ipLoaded) {
            if($this->_items) {
                foreach($this->_items as $item) {
                    $item->setIp(long2ip($item->getIp()));
                }
            }
            $this->_ipLoaded = true;
        }
    }
}
