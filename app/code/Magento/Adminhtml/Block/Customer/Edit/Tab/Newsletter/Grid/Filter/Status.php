<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter subscribers grid website filter
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Edit\Tab\Newsletter\Grid\Filter;

class Status extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\Select
{

    protected static $_statuses;

    protected function _construct()
    {
        self::$_statuses = array(
                null                                        => null,
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
        if(is_null($this->getValue())) {
            return null;
        }

        return array('eq'=>$this->getValue());
    }

}
