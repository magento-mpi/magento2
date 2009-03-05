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
class Enterprise_Invitation_Block_Adminhtml_Invitation_Add extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected $_objectId = 'invitation_id';
    protected $_blockGroup = 'invitation';
    protected $_controller = 'adminhtml_invitation';
    protected $_mode = 'add';

    /**
     * Prepares form scripts
     *
     * @return Enterprise_Invitation_Block_Adminhtml_Invitation_Add
     */
    protected function _prepareLayout()
    {
        $validationMessage = $this->helper('enterprise_invitation')->__(
            'Please enter a valid email addresses, separated by new line.'
        );

        $validationMessage = addcslashes($validationMessage, "\\'\n\r");
        $this->_formInitScripts[] = "
        Validation.addAllThese([
            ['validate-emails', '$validationMessage', function (v) {
                if(Validation.get('IsEmpty').test(v)) {
                    return true;
                }

                var valid_regexp = /^[a-z0-9\\._-]{1,30}@([a-z0-9_-]{1,30}\\.){1,5}[a-z]{2,4}$/i;
                var split_regexp = /[;|\\s|,]/g;
                RegExp.multiline = true;
                if (console) {
                	console.log(split_regexp);
                }
                var emails = v.split();

                for (var i=0; i < emails.length; i++) {
                    if(!valid_regexp.test(emails[i].strip())) {
                        return false;
                    }
                }

                return true;
            }]
        ]);";

        return parent::_prepareLayout();
    }

    public function getHeaderText()
    {
        return Mage::helper('enterprise_invitation')->__('Create Invitations');
    }

}