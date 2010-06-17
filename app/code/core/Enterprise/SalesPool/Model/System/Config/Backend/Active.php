<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_SalesPool
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Pool configuration active flag backend
 *
 */
class Enterprise_SalesPool_Model_System_Config_Backend_Active extends Mage_Core_Model_Config_Data
{
    /**
     * Checks configuration value and run flush and sync actions if it changed
     *
     * @return Enterprise_SalesPool_Model_System_Config_Backend_Active
     */
    protected function _afterSave()
    {
        parent::_afterSave();
        if ($this->isValueChanged()) {
             Mage::getSingleton('enterprise_salespool/pool')->flushAllOrders();
             Mage::getSingleton('enterprise_salespool/pool')->syncAutoincrement();
        }
        return $this;
    }

    public function getCommentText($element, $value)
    {
        $comment = '';
        if (!$value) {
            $id  = $element->getName();
            $url = Mage::helper('adminhtml')->getUrl('*/sales_order_pool/check');
            $onclick = "new Ajax.Updater('database-check-result".$id."', '".$url."', {evalScripts: true});return false;";
            $comment = Mage::helper('enterprise_salespool')->__('Please <a href="#" onclick="%s">check</a> your installation before enabling this option.', $onclick);
            $comment.= '<div id="database-check-result'.$id.'"></div>';
        }
        return $comment;
    }
}