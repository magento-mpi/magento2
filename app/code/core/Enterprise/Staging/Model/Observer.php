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
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Enterprise_Staging Observer class. 
 * 
 * Typical procedure is next:
 * 
 */
class Enterprise_Staging_Model_Observer
{
    /**
     * Get staging table name for the entities whithin staging website navigation
     *
     * @param $observer Varien_Object  
     * expected 'resource', 'model_entity' and 'table_name' variables setted in event object as well
     *
     */
    public function getTableName($observer)
    {
    	try {
	    	$resource = $observer->getEvent()->getResource();
	        $tableName = $observer->getEvent()->getTableName();
	        $modelEntity = $observer->getEvent()->getModelEntity();

	        $config = Mage::getResourceSingleton('enterprise_staging/config');
	        $_tableName = $config->getStagingTableName($tableName, $modelEntity);
	        
	        if ($_tableName) {
	            $resource->setMappedTableName($tableName, $_tableName);
	        }
    	} catch (Enterprise_Staging_Exception $e) {
    		echo '<pre>';
    		echo '<br /><br />';
    		echo $e;
    		echo '<br /><br />';
    		mageDebugBacktrace();
    		echo '</pre>';
    		die(__CLASS__);
    	}
    }
    
    public function beforeFrontendInit()
    {
    	$website = Mage::app()->getWebsite();
    	if ($website->getIsStaging()) {
    		$coreSession = Mage::getSingleton('core/session');
    		$key = 'allow_view_staging_website_'.$website->getCode();

    		if (isset($_SERVER['PHP_AUTH_USER'])) {
                $login      = $_SERVER['PHP_AUTH_USER'];
                $password   = $_SERVER['PHP_AUTH_PW'];
                $website    = Mage::getModel('enterprise_staging/staging_website');
                
                try {
                    $website->authenticate($login, $password);
                    $coreSession->setData($key, true);
                } catch (Exception $e) {
                    $coreSession->setData($key, false);
                    echo '<pre>';
                    echo $e; STOP();
                }
    		}
   			if (!$coreSession->getData($key) || !isset($_SERVER['PHP_AUTH_USER'])) {
                header('WWW-Authenticate: Basic realm="Site Authentication"');
                header('HTTP/1.0 401 Unauthorized');
                echo "Please, use right login and password \n";
                exit();
   			}
    	}
    }
}