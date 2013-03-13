<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Users
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User statuses option array
 *
 * @category   Enterprise
 * @package    Enterprise_GiftWrapping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftWrapping_Model_StatusOptions implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Backend Helper
     *
     * @var Enterprise_GiftWrapping_Helper_Data
     */
    protected $_helper;

    /**
     * @param Enterprise_GiftWrapping_Helper_Data $helper
     */
    public function __construct(Enterprise_GiftWrapping_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array('0' => $this->_helper->__('Disabled'),
                     '1' => $this->_helper->__('Enabled'));
    }
}
