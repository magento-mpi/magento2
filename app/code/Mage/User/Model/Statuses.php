<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_User
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User statuses option array
 *
 * @category   Mage
 * @package    Mage_User
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_User_Model_Statuses implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * User Helper
     *
     * @var Mage_User_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_User_Helper_Data $userHelper
     */
    public function __construct(Mage_User_Helper_Data $userHelper)
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
