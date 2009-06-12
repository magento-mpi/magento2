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
class Enterprise_Invitation_Model_Observer
{
    /**
     * Flag that indicates customer registration page
     *
     * @var boolean
     */
    protected $_flagInCustomerRegistration = false;

    /**
     * Observe customer registration for invitations
     *
     * @return void
     */
    public function restrictCustomerRegistration(Varien_Event_Observer $observer)
    {
        if (! Mage::helper('enterprise_invitation')->isEnabled()) {
            return;
        }

        $result = $observer->getEvent()->getResult();

        if (!$result->getIsAllowed()) {
            Mage::helper('enterprise_invitation')->isRegistrationAllowed(false);
        } else {
            Mage::helper('enterprise_invitation')->isRegistrationAllowed(true);
            $result->setIsAllowed(!Mage::helper('enterprise_invitation')->getInvitationRequired());
        }
    }

    /**
     * special handler for invitation massCancel.
     *
     * @param array $config - event config
     */
    public function postDispatchLoggingInvitationMassCancel($config, $eventModel)
    {
        return $eventModel->setInfo(implode(', ', Mage::app()->getRequest()->getParam('invitations')));
    }

    /**
     * Special after-save handler for invitation.
     * We have a lot of invitations saved (one per each email).
     * This method creates model stub and puts all ids into it
     * separated by ','
     *
     * @param Enterprise_Invitation_Model_Invitation $model
     * @param Varien_SimpleXml_Element
     */
    public function loggingInvitationSaveAfter($model, $config)
    {
        if ($model instanceof Enterprise_Invitation_Model_Invitation) {
            if ($obj = Mage::registry('enterprise_logging_saved_model_adminhtml_invitation_save')) {
                $ids = $obj->getId();
                $ids .= ", ".$model->getId();
                /**
                 * Add one more id to list. This trick allows use
                 * standart post-dispatch observer.
                 */
                $obj->setId($ids);
                Mage::unregister('enterprise_logging_saved_model_adminhtml_invitation_save');
                Mage::register('enterprise_logging_saved_model_adminhtml_invitation_save', $obj);
            } else {
                /**
                 * Create 'stub' model.
                 */
                $stub = Mage::getModel('enterprise_invitation/invitation');
                $stub->setId($model->getId());
                Mage::register('enterprise_logging_saved_model_adminhtml_invitation_save', $stub);
            }
        }
    }
}
