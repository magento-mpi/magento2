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
 * @category   Enterprise
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Invitation data model
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Model_Invitation extends Mage_Core_Model_Abstract
{
    const STATUS_SENT = 'sent';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_CANCELED = 'canceled';

    /**
     * Intialize model
     *
     * @return void
     **/
    protected function _construct()
    {
        $this->_init('enterprise_invitation/invitation');
    }

    /**
     * Genrates protection code
     *
     * @return string
     */
    public function generateCode()
    {
        $code = md5(microtime(true));
        return $code;
    }

    /**
     * Return encoded invitation code
     *
     * @return string
     */
    public function getInvitationCode()
    {
        $code = Mage::helper('core')->encrypt(
            $this->getId() . ':' . $this->getProtectionCode()
        );

        return Mage::helper('core')->urlEncode($code);
    }

    /**
     * Load invitation by code generated with
     * Enterprise_Invitation_Model_Invitation::getInvitationCode(),
     * and check if invitation has status not equal to "sent".
     *
     * @param string $code
     * @return Enterprise_Invitation_Model_Invitation
     */
    public function loadByInvitationCode($code)
    {
        $code = Mage::helper('core')->urlDecode($code);

        $code = explode(':',
            Mage::helper('core')->decrypt($code)
        );

        if (count($code) != 2) {
             return $this;
        }

        $this->load($code[0]);

        if ($this->getProtectionCode() != $code[1] ||
            $this->getStatus() != self::STATUS_SENT) {
            $this->unsetData();
            $this->setOrigData();
        }

        return $this;
    }


    /**
     * Handling status change on after save
     *
     * @return Enterprise_Invitation_Model_Invitation
     */
    protected function _afterSave()
    {
        if ($this->dataHasChangedFor('status')) {
            $now = Mage::app()->getLocale()->date()
                    ->setTimezone(Mage_Core_Model_Locale::DEFAULT_TIMEZONE)
                    ->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $statusHistory = Mage::getModel('enterprise_invitation/invitation_status_history');
            $statusHistory->setInvitationId($this->getId())
                ->setStatus($this->getStatus())
                ->setDate($now)
                ->save();
        }

        return parent::_afterSave();
    }

    /**
     * Return status history collection
     *
     * @return Enterprise_Invitation_Model_Mysql4_Invitation_Status_History_Collection
     */
    public function getStatusHistoryCollection()
    {
        if (!$this->hasData('status_history_collection')) {
            $collection = Mage::getModel('enterprise_invitation/invitation_status_history')
                ->getCollection()
                ->addFieldToFilter('invitation_id', $this->getId());
            $this->setData('status_history_collection', $collection);
        }

        return $this->getData('status_history_collection');
    }
}
