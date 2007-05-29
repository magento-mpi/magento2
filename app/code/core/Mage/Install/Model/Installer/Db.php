<?php
/**
 * DB Installer
 *
 * @package     Mage
 * @subpackage  Install
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_Model_Installer_Db extends Mage_Install_Model_Installer 
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    /**
     * Create database
     * 
     * $data = array(
     *      [db_host]
     *      [db_name]
     *      [db_user]
     *      [db_pass]
     * )
     * 
     * @param array $data
     */
    public function checkDatabase($data)
    {
        $config = array(
            'host'      => $data['db_host'],
            'username'  => $data['db_user'],
            'password'  => $data['db_pass'],
            'dbname'    => $data['db_name']
        );
        
        try {
            $connection = Mage::registry('resources')->createConnection('install', 'mysqli', $config);
            $connection->query('SELECT 1');
        }
        catch (Exception $e){
            Mage::getSingleton('install', 'session')->addMessage(
                Mage::getModel('core', 'message')->error($e->getMessage())
            );
            throw new Exception('Database connection error');
        }
    }
}