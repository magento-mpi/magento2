<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;

/**
 * Customer reset password form
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Resetpassword extends \Magento\View\Element\Template
{
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }
}
