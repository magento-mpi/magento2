<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Centinel\Controller\Index;

class AuthenticationStart extends \Magento\Centinel\Controller\Index
{
    /**
     * Process autentication start action
     *
     * @return void
     */
    public function execute()
    {
        $validator = $this->_getValidator();
        if ($validator) {
            $this->_coreRegistry->register('current_centinel_validator', $validator);
        }
        $this->_view->loadLayout()->renderLayout();
    }
}
