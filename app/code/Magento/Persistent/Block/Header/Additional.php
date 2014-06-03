<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Persistent\Block\Header;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;

/**
 * Remember Me block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Additional extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerViewHelper;

    /**
     * @var \Magento\Persistent\Helper\Session
     */
    protected $_persistentSessionHelper;

    /**
     * @var CustomerAccountServiceInterface
     */
    protected $_customerAccountService;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\View $customerViewHelper
     * @param \Magento\Persistent\Helper\Session $persistentSessionHelper
     * @param CustomerAccountServiceInterface $customerAccountService
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Persistent\Helper\Session $persistentSessionHelper,
        CustomerAccountServiceInterface $customerAccountService,
        array $data = array()
    ) {
        $this->_customerViewHelper = $customerViewHelper;
        $this->_persistentSessionHelper = $persistentSessionHelper;
        $this->_customerAccountService =  $customerAccountService;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve unset cookie link
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('persistent/index/unsetCookie');
    }

    /**
     * Render additional header html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $persistentName = $this->_escaper->escapeHtml(
            $this->_customerViewHelper->getCustomerName(
                $this->_customerAccountService->getCustomer(
                    $this->_persistentSessionHelper->getSession()->getCustomerId()
                )
            )
        );
        return '<span><a ' . $this->getLinkAttributes() . ' >' . $this->escapeHtml(__('(Not %1?)', $persistentName))
        . '</a></span>';
    }
}
