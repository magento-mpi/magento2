<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Customer Widget Form Boolean Element Block
 *
 * @category    Magento
 * @package     Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Adminhtml\Form\Element;

class Boolean extends \Magento\Data\Form\Element\Select
{
    /**
     * Prepare default SELECT values
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setValues(array(array('label' => __('No'), 'value' => '0'), array('label' => __('Yes'), 'value' => 1)));
    }
}
