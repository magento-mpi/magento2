<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit;

use Magento\Ui\Component\Control\ButtonProviderInterface;

/**
 * Class ResetButton
 * @package Magento\Customer\Block\Adminhtml\Edit
 */
class ResetButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Reset'),
            'class' => 'reset',
            'on_click' => 'setLocation(window.location.href)',
            'sort_order' => 50
        ];
    }
}
