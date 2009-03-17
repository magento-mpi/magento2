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

class Enterprise_Logging_Model_Mysql4_Event extends Mage_Core_Model_Mysql4_Abstract 
{
    protected $_users;
    protected $_actions;

   /**
    * Constructor
    */
    protected function _construct() 
    {
        $this->_init('enterprise_logging/event', 'event_id');
    }

    /**
     * Before save ip convertor
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $event)
    {
        $event->setData('ip', ip2long($event->getIp()));
        $event->setTime($this->formatDate($event->getTime()));
    }

    /**
     * Rotate function
     */
    public function rotate($interval)
    {
        $path = Mage::getModel('enterprise_logging/logs')->getBasePath();
        $dir = $path . DS . date("Y") . DS . date("m");
        $outfile = sprintf("%s%s%s.csv",  $dir, DS, date("Ymdh"));

        $file = new Varien_Io_File();
        $file->setAllowCreateFolders(true);
        $file->createDestinationDir($dir);

        $lifetime = (string)Mage::getConfig()->getNode('default/system/rotation/lifetime');
        $lifetime = (int)$lifetime;
        $table = $this->getTable('enterprise_logging/event');
        /** 
         * Be sure, that $outfile is reacheable for mysql user, and 
         * security tools like SeLinux, or apparmor are disabled or allows 
         * mysql to create $outfile
         */
        $query = sprintf("SELECT * FROM %s WHERE time + INTERVAL %s DAY < NOW()", $table, $lifetime);
        $del_query = sprintf("DELETE FROM %s WHERE time + INTERVAL %s DAY < NOW()", $table, $lifetime);
        $st = $this->_getConnection('write')->query($query);

        $f = fopen($outfile, "w");
        while ($row = $st->fetch()) {
            fputcsv($f, $row);
        }
        fclose($f);
        $this->_getConnection('write')->query($del_query);
    }

    /**
     * Get list of actions presented in event table
     */
    public function getActions() {
        if(!$this->_actions) {
            $query = "SELECT DISTINCT action FROM ".$this->getTable('enterprise_logging/event');
            $st = $this->_getConnection('read')->query($query);
            $actions = $st->fetchAll();
            $this->_actions = array();
            if($actions) {
                foreach($actions as $action) {
                    $u = new Varien_Object();
                    $u->setId($action['action']);
                    $u->setName($action['action']);
                    $this->_actions[] = $u; 
                }
            }
        }
        return $this->_actions;
    }

    /**
     * Get list of users presented in event table
     */
    public function getUsers() {
        if(!$this->_users) {
            $query = "SELECT DISTINCT user FROM ".$this->getTable('enterprise_logging/event');
            $st = $this->_getConnection('read')->query($query);
            $users = $st->fetchAll();
            $this->_users = array();
            if($users) {
                foreach($users as $user) {
                    $u = new Varien_Object();
                    $u->setId($user['user']);
                    $u->setUsername($user['user']);
                    $this->_users[] = $u; 
                }
            }
        }
        return $this->_users;
    }
}
