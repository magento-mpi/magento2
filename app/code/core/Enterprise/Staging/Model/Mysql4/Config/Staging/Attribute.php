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
 * Enterprise Staging Attributes Config class. 
 * 
 */
class Enterprise_Staging_Model_Mysql4_Config_Staging_Attribute extends Enterprise_Staging_Model_Mysql4_Config
{
	static protected $_attrGroups = array();

	public function __construct()
	{
		parent::__construct();
	}
    
    static public function getOptionArray()
    {
        $groups = array();
        foreach (self::getAttributeGroups() as $groupId => $group) {
            $groups[$groupId] = $group['label'];
        }
        return $groups;
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
        $res[] = array('value'=>'', 'label'=>'');
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }
    
    static public function getOptions()
    {
        $res = array();
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
        $options = self::getAttributeGroupsArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
    
    
    
    static public function getAttributeGroups()
    {
        if (is_null(self::$_attrGroups)) {
            self::$_attrGroups = Mage::getConfig()->getNode('global/enterprise/staging/type')->asArray();
        }
        return self::$_attrGroups;
    }
}