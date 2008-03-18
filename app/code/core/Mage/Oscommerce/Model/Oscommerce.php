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
 * Convert profile resource model
 *
 * @category   Mage
 * @package    Mage_OsCommerce
 * @author     Kyaw Soe Lynn Maung <vincent@varien.com>
 */
class Mage_Oscommerce_Model_Oscommerce extends Mage_Core_Model_Abstract
{
	const DEFAULT_PORT = 3360;
	const CONNECTION_TYPE = 'pdo_mysql';
	const CONNECTION_NAME = 'oscommerce_db';
	
    protected function _construct()
    {
        $this->_init('oscommerce/oscommerce');
    }
    
    public function createConnection($id)
    {
    	$model = Mage::registry('current_convert_osc');
    	if ($model->getId()) {
    		$data = $model->getData();
    	} else {
    		$this->load($id);
    		$data = $this->getData();
    	}
    	$config = array(
    	'host' 		=> $data['host'],
    	'username' 	=> $data['db_user'],
    	'password' 	=> $data['db_password'],
    	'dbname' 	=> $data['db_name'],
    	'active'	=> 1
    	);

    	$connection = new Mage_Core_Model_Resource();
    	$connection->createConnection(self::CONNECTION_NAME , self::CONNECTION_TYPE , $config);
    	if ($connection) {
    		Mage::register('osc_db_connection', $connection);
    		return $connection;
    	}
    }
    
    public function getConnection()
    {
    	if ($conn = Mage::registry('osc_db_connection')) {
    		return $conn;
    	}
    	return false;
    }
    
    public function getProducts() 
    {
    	$conn = $this->getConnection();
    	if ($conn) {
    		$select = $conn->select()->from('products', array('*'));
    		$test = $conn->fetchAll($select);
    		print_r($test);
    	}
    }
}
