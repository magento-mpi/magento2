<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;

/**
 * Customer account navigation sidebar
 */
class Forgotpassword extends \Magento\View\Element\Template
{
    /**
     * Forgot Password Constructor.
     *
     * @param \Magento\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }
}
