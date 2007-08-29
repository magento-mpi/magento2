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


class Mage_Adminhtml_Model_System_Config_Source_Email_Identity
{
    public function toOptionArray()
    {
       	$identities = Mage::getResourceModel('core/config_field_collection')
       		->addFieldToFilter("level", 2)
       		->addFieldToFilter("path", array('like'=>'trans_email/ident_%'))
       		->load();
       		
        $arr = array();
       	foreach ($identities as $ident) {
			$arr[] = array(
				'value' => preg_replace('#^trans_email/ident_(.*)$#', '$1', $ident->getPath()),
				'label' => $ident->getFrontendLabel(),
			);
       	}
        
        return $arr;
    }
}