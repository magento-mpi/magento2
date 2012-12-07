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
 * Class which creates mock object for models when they are created
 * in a code using Mage::getResourceSingleton('...');
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_MockBuilder_Model_ResourceSingleton extends Mage_PHPUnit_MockBuilder_Model_Singleton
{
    /**
     * Singleton key prefix needed for Mage::registry
     *
     * @var string
     */
    protected $_registryKeyPrefix = '_resource_singleton';

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
