<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Invitation view block
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Adminhtml_Invitation_Add extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_objectId = 'invitation_id';
    protected $_blockGroup = 'Enterprise_Invitation';
    protected $_controller = 'adminhtml_invitation';
    protected $_mode = 'add';

    /**
     * Prepares form scripts
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Invitation_Add
     */
    protected function _prepareLayout()
    {
        $validationMessage = addcslashes(Mage::helper('Enterprise_Invitation_Helper_Data')->__('Please enter valid email addresses, separated by new line.'), "\\'\n\r");
        $this->_formInitScripts[] = "
        Validation.addAllThese([
            ['validate-emails', '$validationMessage', function (v) {
                v = v.strip();
                var emails = v.split(/[\\s]+/g);
                for (var i = 0, l = emails.length; i < l; i++) {
                    if (!Validation.get('validate-email').test(emails[i])) {
                        return false;
                    }
                }
                return true;
            }]
        ]);";
        return parent::_prepareLayout();
    }

    /**
     * Get header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        return Mage::helper('Enterprise_Invitation_Helper_Data')->__('New Invitations');
    }

}
