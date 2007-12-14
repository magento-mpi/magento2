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
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Config_Xml 
{
    function getGroups ($sectionCode=null, $websiteCode=null, $storeCode=null){
        
        $mergeConfig = new Mage_Core_Model_Config_Base();
        
        $config = Mage::getConfig();
        $modules = $config->getNode('modules')->children();
        foreach ($modules as $modName=>$module) {
            if ($module->is('active')) {
                $configFile = $config->getModuleDir('etc', $modName).DS.'system.xml';
                if ($mergeConfig->loadFile($configFile)) {
                    $config->extend($mergeConfig, true);
                }
            }
        }
        $config->applyExtends();


        if ($sectionCode) {
//        echo '<pre>';
//        print_r($config->getNode()->groups->$sectionCode);
//        echo '</pre>';
        	return $config->getNode()->groups->$sectionCode;
        }
        if ($websiteCode) {
//                    echo '<pre>';
//        print_r($config->getNode()->groups->$websiteCode);
//        echo '</pre>';
        	return $config->getNode()->groups->$websiteCode;
        }
        if ($storeCode) {
//            echo '<pre>';
//        print_r($config->getNode()->groups->$storeCode);
//        echo '</pre>';
        	return $config->getNode()->groups->$storeCode;
        }

        

       

    }
}