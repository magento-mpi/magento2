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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Front end helper block to render form
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Form extends Mage_Core_Block_Template
{
    /**
     * Returns maximal number of invitations to send in one try
     *
     * @return int
     */
    public function getMaxInvitationsPerSend()
    {
        return Mage::getSingleton('Enterprise_Invitation_Model_Config')->getMaxInvitationsPerSend();
    }

    /**
     * Returns whether custom invitation message allowed
     *
     * @return bool
     */
    public function isInvitationMessageAllowed()
    {
        return Mage::getSingleton('Enterprise_Invitation_Model_Config')->isInvitationMessageAllowed();
    }
}
