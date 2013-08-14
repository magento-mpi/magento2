<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rule
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract Rule entity data model
 *
 * @deprecated since 1.7.0.0 use Magento_Rule_Model_Abstract instead
 *
 * @category Magento
 * @package Magento_Rule
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Magento_Rule_Model_Rule extends Magento_Rule_Model_Abstract
{
    /**
     * Getter for rule combine conditions instance
     *
     * @return Magento_Rule_Model_Condition_Combine
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('Magento_Rule_Model_Condition_Combine');
    }

    /**
     * Getter for rule actions collection instance
     *
     * @return Magento_Rule_Model_Action_Collection
     */
    public function getActionsInstance()
    {
        return Mage::getModel('Magento_Rule_Model_Action_Collection');
    }
}
