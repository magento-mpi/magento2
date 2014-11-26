<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Ui\Component\Control\ButtonProviderInterface;

/**
 * Class SaveAndContinueButton
 * @package Magento\Customer\Block\Adminhtml\Edit
 */
class SaveAndContinueButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @var CustomerAccountServiceInterface
     */
    protected $customerAccountService;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param CustomerAccountServiceInterface $customerAccountService
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        CustomerAccountServiceInterface $customerAccountService
    ) {
        parent::__construct($context, $registry);
        $this->customerAccountService = $customerAccountService;
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $canModify = !$customerId || $this->customerAccountService->canModify($this->getCustomerId());
        $data = [];
        if ($canModify) {
            $data = [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit')
                    )
                ),
                'sort_order' => 80
            ];
        }
        return $data;
    }
}
