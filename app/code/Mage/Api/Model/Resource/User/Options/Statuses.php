<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Api User Resource Options Statuses
 *
 * @category    Mage
 * @package     Mage_Api
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api_Model_Resource_User_Options_Statuses implements Mage_Core_Model_Option_ArrayInterface
{

    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Backend_Helper_Data $helper
     */
    public function __construct(Mage_Backend_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Return options
     *
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
