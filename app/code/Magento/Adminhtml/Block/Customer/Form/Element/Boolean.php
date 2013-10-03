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
 * Customer Widget Form Boolean Element Block
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Form\Element;

class Boolean extends \Magento\Data\Form\Element\Select
{
    /**
     * Prepare default SELECT values
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setValues(array(
            array(
                'label' => __('No'),
                'value' => '0',
            ),
            array(
                'label' => __('Yes'),
                'value' => 1,
            )
        ));
    }
}
