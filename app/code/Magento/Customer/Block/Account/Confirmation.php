<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Account;
use Magento\Customer\Model\Url;
use Magento\Framework\View\Element\Template;

/**
 * Customer account navigation sidebar
 */
class Confirmation extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Url
     */
    protected $customerUrl;

    /**
     * @param Template\Context $context
     * @param Url $customerUrl
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Url $customerUrl,
        array $data = array()
    ) {
        $this->customerUrl = $customerUrl;
        parent::__construct($context, $data);
    }

    public function getLoginUrl()
    {
        return $this->customerUrl->getLoginUrl();
    }
}
