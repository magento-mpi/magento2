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
 * Staging config model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Config
{
	static $_config;
	
	static $_entities;
	
    const VISIBILITY_NOT_VISIBLE            = 1;
    const VISIBILITY_WHILE_MASTER_LOGIN     = 2;
    const VISIBILITY_WHILE_ADMIN_SESSION    = 3;
    const VISIBILITY_BOTH                   = 4;
    const VISIBILITY_VISIBLE                = 5;

    static public function getConfig()
    {
        if (is_null(self::$_config)) {
        	self::$_config = Mage::getConfig()->getNode('global/enterprise/staging');
        }
        return self::$_config;
    }
    
    static public function getAvailableEntities()
    {
    	if (is_null(self::$_entities)) {
    		$config = self::getConfig();
    		if ($config) {
    			$entities = $config->staging_entities;
    			if ($entities) {
    				foreach ((array) $entities as $entity) {
    					$entity = (array) $entity;
    					self::$_entities[$entity['code']] = $entity;
    				}
    			}
    		}
    	}

    	return self::$_entities;
    }
    
    static public function checkCurrentVisibility($staging)
    {
    	/* TODO need to return visibility status according current permission level and so on ... */
    	return true;
    }
    
    static public function addVisibleFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $collection->setVisibility($this->getVisibleIds());
    }
    
    static public function getVisibleIds()
    {
    	/* TODO need to build list according current permission level and so on ... */
        return array(
            self::VISIBILITY_WHILE_MASTER_LOGIN,
            self::VISIBILITY_WHILE_ADMIN_SESSION,
            self::VISIBILITY_BOTH,
            self::VISIBILITY_VISIBLE
        );
    }

    static public function getOptionArray()
    {
        return array(
            self::VISIBILITY_NOT_VISIBLE            => Mage::helper('enterprise_staging')->__('Not Visible'),
            self::VISIBILITY_WHILE_MASTER_LOGIN      => Mage::helper('enterprise_staging')->__('While Master Login'),
            self::VISIBILITY_WHILE_ADMIN_SESSION    => Mage::helper('enterprise_staging')->__('While Admin Session'),
            self::VISIBILITY_BOTH                   => Mage::helper('enterprise_staging')->__('While Admin Session and Master Login'),
            self::VISIBILITY_VISIBLE                => Mage::helper('enterprise_staging')->__('Visible')
        );
    }

    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

    static public function getAllOptions()
    {
        $res = array();
        $res[] = array('value'=>'', 'label'=> Mage::helper('enterprise_staging')->__('-- Please Select --'));
        foreach (self::getOptionArray() as $index => $value) {
        	$res[] = array(
        	   'value' => $index,
        	   'label' => $value
        	);
        }
        return $res;
    }

    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}