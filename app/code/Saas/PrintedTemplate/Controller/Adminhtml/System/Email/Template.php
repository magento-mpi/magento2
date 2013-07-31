<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Override transactions email template controller to chang ACL for it
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  Controllers
 */
class Saas_PrintedTemplate_Controller_Adminhtml_System_Email_Template
    extends Magento_Adminhtml_Controller_System_Email_Template
{
    /**
     * Override to change ACL
     *
     * @return bool
     * @see Magento_Adminhtml_Controller_System_Email_Template::_isAllowed()
     */
    protected function _isAllowed()
    {
        return $this->_authorization
            ->isAllowed('Saas_PrintedTemplate::printed_template');
    }
}
