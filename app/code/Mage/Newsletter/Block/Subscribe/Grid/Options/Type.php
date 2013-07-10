<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter options
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Newsletter_Block_Subscribe_Grid_Options_Type implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * @var Mage_Newsletter_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Newsletter_Helper_Data $newsletterHelper
     */
    public function __construct(Mage_Newsletter_Helper_Data $newsletterHelper)
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
