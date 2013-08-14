<?php
/**
 * Newsletter grid type options
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Newsletter_Block_Subscribe_Grid_Options_Type implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Newsletter_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Newsletter_Helper_Data $newsletterHelper
     */
    public function __construct(Magento_Newsletter_Helper_Data $newsletterHelper)
    {
        $this->_helper = $newsletterHelper;
    }

    /**
     * Return column options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => $this->_helper->__('Guest'),
            '2' => $this->_helper->__('Customer'),
        );
    }
}
