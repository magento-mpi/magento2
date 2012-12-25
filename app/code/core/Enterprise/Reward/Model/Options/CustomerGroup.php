<?php
/**
 * {license_notice}
 *
 * @category    Enterprice
 * @package     Enterprice_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Group options array
 *
 * @category   Enterprise
 * @package    Enterprise_Reward
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Options_CustomerGroup implements Mage_Core_Model_Option_ArrayInterface
{

    /**
     * Reward Source website model
     *
     * @var Enterprise_Reward_Model_Source_Website
     */
    protected $_sourceCustomerGroupModel;

    /**
     * @param Enterprise_Reward_Model_Source_Customer_Groups $customerGroupModel
     */
    public function __construct(Enterprise_Reward_Model_Source_Customer_Groups $customerGroupModel)
    {
        $this->_sourceCustomerGroupModel = $customerGroupModel;
    }

    /**
     * Return customer group options array
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_sourceCustomerGroupModel->toOptionArray();
    }
}
