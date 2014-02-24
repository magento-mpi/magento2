<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Filter;

/**
 * Adminhtml newsletter subscribers grid website filter
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    protected static $_statuses;

    protected function _construct()
    {
        self::$_statuses = array(
                null                                            => null,
                \Magento\Newsletter\Model\Queue::STATUS_SENT    => __('Sent'),
                \Magento\Newsletter\Model\Queue::STATUS_CANCEL  => __('Cancel'),
                \Magento\Newsletter\Model\Queue::STATUS_NEVER   => __('Not Sent'),
                \Magento\Newsletter\Model\Queue::STATUS_SENDING => __('Sending'),
                \Magento\Newsletter\Model\Queue::STATUS_PAUSE   => __('Paused'),
            );
        parent::_construct();
    }

    protected function _getOptions()
    {
        $result = array();
        foreach (self::$_statuses as $code=>$label) {
            $result[] = array('value'=>$code, 'label'=>__($label));
        }

        return $result;
    }

    public function getCondition()
    {
        if (is_null($this->getValue())) {
            return null;
        }

        return array('eq' => $this->getValue());
    }
}
