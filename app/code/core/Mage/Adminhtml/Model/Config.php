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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Configuration for Admin model
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Model_Config extends Varien_Simplexml_Config
{

    var $sections;

    function getSections ($sectionCode=null, $websiteCode=null, $storeCode=null){

        if (!isset($this->sections)) {
            $this->takeSections();
        }
        return $this->sections;

    }
    function takeSections ($sectionCode=null, $websiteCode=null, $storeCode=null) {
        if (empty($this->sections)) {
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



            $sections=$config->getNode()->sections->children();



            $this->sections=$sections;

        }
    }
    function getSection ($sectionCode=null, $websiteCode=null, $storeCode=null){

        if (!isset($this->sections)) {
            $this->takeSections();
        }

        if ($sectionCode){
            return  $this->sections->$sectionCode;
        }elseif ($websiteCode) {
            return  $this->sections->$websiteCode;
        } elseif ($storeCode) {
            return  $this->sections->$storeCode;
        }
    }
    public function hasChildren ($node, $websiteCode=null, $storeCode=null, $isField=false){
        $showTab = false;
        if ($storeCode) {
            if (isset($node->show_in_store)) {
                if ((int)$node->show_in_store) {
                    $showTab=true;
                }
            }
        }elseif ($websiteCode) {
            if (isset($node->show_in_website)) {
                if ((int)$node->show_in_website) {
                    $showTab=true;
                }
            }
        } elseif (isset($node->show_in_default)) {
                if ((int)$node->show_in_default) {
                    $showTab=true;
                }
        }
        if ($showTab) {
            if (isset($node->groups)) {
                foreach ($node->groups->children() as $children){
                    if ($this->hasChildren ($children, $websiteCode, $storeCode)) {
                    	return true;
                    }

                }
            }elseif (isset($node->fields)) {

                foreach ($node->fields->children() as $children){
                    if ($this->hasChildren ($children, $websiteCode, $storeCode, true)) {
                    	return true;
                    }
                }
            } else {
                return true;
            }
        }
        return false;

    }
}