<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;

use Magento\View\Element\Template\Context;

/**
 * Customer reset password form
 */
class Resetpassword extends \Magento\View\Element\Template
{
    /**
     * Reset Password Constructor.
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }
}
