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
 * @package    Enterprise_Invitation
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Invitation view block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_View extends Mage_Adminhtml_Block_Widget_Container
{

    protected function _prepareLayout()
    {
        $this->_headerText = Mage::helper('enterprise_invitation')->__(
            'View invitation for %s (ID: %s)',
            $this->getInvitation()->getEmail(),
            $this->getInvitation()->getId()
        );

        $this->_addButton('back', array(
                'label' => Mage::helper('enterprise_invitation')->__('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/') . '\')',
                'class' => 'back',
        ), -1);

        if ($this->getInvitation()->getStatus() == 'sent') {
            $this->_addButton('cancel', array(
                    'label' => Mage::helper('enterprise_invitation')->__('Cancel'),
                    'onclick' => 'deleteConfirm(\''. $this->jsQuoteEscape(
                                Mage::helper('enterprise_invitation')->__('Are you sure you want to do this?')
                            ) . '\', \'' . $this->getUrl('*/*/cancel', array('_current'=>true)) . '\' )',
                    'class' => 'cancel'
            ), -1);

            $this->_addButton('resend', array(
                    'label' => Mage::helper('enterprise_invitation')->__('Re-send'),
                    'onclick' => 'setLocation(\'' . $this->getUrl('*/*/resend', array('_current'=>true)) . '\')'
            ), -1);
        }



        parent::_prepareLayout();
    }

    /**
     * Return invitaion for view
     *
     * @return Portero_Invitation_Model_Invitation
     */
    public function getInvitation()
    {
        return Mage::registry('current_invitation');
    }

    /**
     * Retrieve save message url
     *
     * @return string
     */
    public function getSaveMessageUrl()
    {
        return $this->getUrl('*/*/saveMessage', array('id'=>$this->getInvitation()->getId()));
    }

}