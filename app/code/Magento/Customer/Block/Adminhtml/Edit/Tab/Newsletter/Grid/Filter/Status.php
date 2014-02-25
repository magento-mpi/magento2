<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Adminhtml\Edit\Tab\Newsletter\Grid\Filter;

/**
 * Adminhtml newsletter subscribers grid website filter
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @var array
     */
    protected static $_statuses;

    /**
     * @return void
     */
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

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $result = array();
        foreach (self::$_statuses as $code=>$label) {
            $result[] = array('value'=>$code, 'label'=>__($label));
        }

        return $result;
    }

    /**
     * @return array|null
     */
    public function getCondition()
    {
        if (is_null($this->getValue())) {
            return null;
        }

        return array('eq'=>$this->getValue());
    }

}
