<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User statuses option array
 *
 * @category   Magento
 * @package    Magento_User
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_User_Model_Statuses implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * User Helper
     *
     * @var Magento_User_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_User_Helper_Data $userHelper
     */
    public function __construct(Magento_User_Helper_Data $userHelper)
    {
        $this->_helper = $userHelper;
    }

    /**
     * Return statuses array
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => $this->_helper->__('Active'),
            '0' => $this->_helper->__('Inactive'),
        );
    }
}
