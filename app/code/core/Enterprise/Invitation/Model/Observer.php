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
 * @package     Enterprise_Invitation
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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

    protected $_config;

    public function __construct()
    {
        $this->_config = Mage::getSingleton('enterprise_invitation/config');
    }

    /**
     * Observe customer registration for invitations
     *
     * @return void
     */
    public function restrictCustomerRegistration(Varien_Event_Observer $observer)
    {
        if (!$this->_config->isEnabledOnFront()) {
            return;
        }

        $result = $observer->getEvent()->getResult();

        if (!$result->getIsAllowed()) {
            Mage::helper('enterprise_invitation')->isRegistrationAllowed(false);
        } else {
            Mage::helper('enterprise_invitation')->isRegistrationAllowed(true);
            $result->setIsAllowed(!$this->_config->getInvitationRequired());
        }
    }

    /**
     * Custom log invitation log action
     *
     * @param Interprise_Invitation_Model_Invitation $model
     * @param Enterprise_Logging_Model_Processor $processor
     * @return Enterprise_Logging_Model_Event_Changes
     */
    public function logInvitationSave($model, $processor)
    {
        $processor->collectId($model);
        $data = $processor->cleanupData($model->getData());
        return Mage::getModel('enterprise_logging/event_changes')
            ->setData(array('original_data' => array(), 'result_data' => $data));
    }
}
