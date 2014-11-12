<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Control\ButtonProviderInterface;

/**
 * Class ResetPasswordButton
 * @package Magento\Customer\Block\Adminhtml\Edit
 */
class ResetPasswordButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $customerId = $this->getCustomerId();
        $data = [];
        if ($customerId) {
            $data = [
                'label' => __('Reset Password'),
                'class' => 'reset reset-password',
                'on_click' => 'setLocation(\'' . $this->getResetPasswordUrl() . '\')',
                'sort_order' => 40
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getResetPasswordUrl()
    {
        return $this->getUrl('customer/index/resetPassword', array('customer_id' => $this->getCustomerId()));
    }
}
