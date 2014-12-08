<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reward\Model\Action\Creditmemo;

class Void extends \Magento\Reward\Model\Action\Creditmemo
{
    /**
     * Return action message for history log
     *
     * @param array $args additional history data
     * @return string
     */
    public function getHistoryMessage($args = [])
    {
        $incrementId = isset($args['increment_id']) ? $args['increment_id'] : '';
        return __('Points voided at order #%1 refund.', $incrementId);
    }
}
