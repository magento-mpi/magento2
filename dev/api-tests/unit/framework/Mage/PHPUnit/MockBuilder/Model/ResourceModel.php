<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Class which creates mock object for resource models when they are created
 * in a code using Mage::getResourceModel('...');
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_MockBuilder_Model_ResourceModel extends Mage_PHPUnit_MockBuilder_Model_Model
{
    /**
     * Delegators pool key. Needed different pool for resource models
     *
     * @var string
     */
    protected $_delegatorsPool = Mage_PHPUnit_StaticDataPoolContainer::POOL_RESOURCE_MODEL_DELEGATORS;

    /**
     * Returns PHPUnit model helper.
     *
     * @return Mage_PHPUnit_Helper_Model_ResourceModel
     */
    protected function _getModelHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('model_resourceModel');
    }
}
