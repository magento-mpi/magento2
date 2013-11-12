<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\App\Action;

class FormKeyValidator
{
    /**
     * @param \Magento\Core\Model\Session $session
     */
    public function __construct(\Magento\Core\Model\Session $session)
    {
        $this->_session = $session;
    }

    /**
     * Validate form key
     *
     * @param \Magento\App\RequestInterface $request
     * @return bool
     */
    public function validate(\Magento\App\RequestInterface $request)
    {
        $formKey = $request->getParam('form_key', null);
        if (!$formKey || $formKey != $this->_session->getFormKey()) {
            return false;
        }
        return true;
    }
} 