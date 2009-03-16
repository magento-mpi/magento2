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
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_CustomerBalance_Model_Balance_History extends Mage_Core_Model_Abstract
{
    const ACTION_EVENT_CREATE = 1;
    const ACTION_EVENT_UPDATE = 2;

    protected function _construct()
    {
        $this->_init('enterprise_customerbalance/balance_history');
    }

    public function addCreateEvent($object)
    {
        $this->setAction(self::ACTION_EVENT_CREATE)
             ->setAdminUser(Mage::getSingleton('admin/session')->getUser()->getUsername())
             ->setNotified($object->getEmailNotify());

        $this->_addEvent($object);
        return $this;
    }

    public function addUpdateEvent($object)
    {
        $this->setAction(self::ACTION_EVENT_UPDATE)
             ->setAdminUser(Mage::getSingleton('admin/session')->getUser()->getUsername())
             ->setNotified($object->getEmailNotify());

        $this->_addEvent($object);
        return $this;
    }

    protected function _addEvent($object)
    {
        $this->setCustomerId($object->getCustomerId())
             ->setWebsiteId($object->getWebsiteId())
             ->setDelta($object->getDelta())
             ->setBalance($object->getBalance())
             ->setDate($this->_getResource()->formatDate(time()))
             ->save();

         return $this;
    }

    public function getActionName()
    {
    	$actions = $this->getActionNamesArray();
        return $actions[$this->getAction()];
    }
    
    public function getActionNamesArray()
    {
        return array(
            self::ACTION_EVENT_CREATE => Mage::helper('enterprise_customerbalance')->__('Created'),
            self::ACTION_EVENT_UPDATE => Mage::helper('enterprise_customerbalance')->__('Updated'),
        );    	
    }

    public function getFormattedDelta()
    {
        return Mage::app()->getStore()->formatPrice($this->getDelta());
    }
}