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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Data change history model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Mysql4_History
{
    protected $_changeTable = null;
    protected $_changeInfoTable = null;
    
    public function __construct() 
    {
        $this->_changeTable = Mage::getSingleton('core/resource')->getTableName('core/data_change');
        $this->_changeInfoTable = Mage::getSingleton('core/resource')->getTableName('core/data_change_info');
    }
    
    /**
     * Add data changes
     * 
     * $data = array(
     *      [$tableName] => array(
     *          [pk_value]
     *          [type] = 'insert' || 'update' || 'delete'
     *          [before]
     *          [after]
     *      )
     * )
     * 
     * @param string $code
     * @param int $userId
     * @param array $data
     */
    public function addChanges($code, $userId, $data)
    {
        
    }
}