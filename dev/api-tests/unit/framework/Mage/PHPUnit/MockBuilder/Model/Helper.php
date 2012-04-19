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
 * Class which creates mock object for Mage::helper() construction
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_MockBuilder_Model_Helper extends Mage_PHPUnit_MockBuilder_Model_Singleton
{
    /**
     * Singleton key prefix needed for Mage::registry
     *
     * @var string
     */
    protected $_regisrtyKeyPrefix = '_helper';

    /**
     * Returns PHPUnit model helper.
     *
     * @return Mage_PHPUnit_Helper_Model_Helper
     */
    protected function _getModelHelper()
    {
        return Mage_PHPUnit_Helper_Factory::getHelper('model_helper');
    }

    /**
     * Returns full registry key.
     *
     * @param string $modelKey
     * @return string
     */
    protected function _getRegisrtyKey($modelKey)
    {
        if (strpos($modelKey, '/') === false) {
            $modelKey .= '/data';
        }

        return parent::_getRegisrtyKey($modelKey);
    }
}
