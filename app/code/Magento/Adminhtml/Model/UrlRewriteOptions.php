<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Users
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User statuses option array
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Model_UrlRewriteOptions implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Backend Helper
     *
     * @var Magento_Adminhtml_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Adminhtml_Helper_Data $helper
     */
    public function __construct(Magento_Adminhtml_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array('1' => $this->_helper->__('System'),
                     '0' => $this->_helper->__('Custom'));
    }
}
