<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Custom handlers for models logging
 *
 * All handlers must take 2 params: $model and $config objects,
 * where model is the affected entity instance and config is the configuration node for respective full action name
 */
class Enterprise_Logging_Model_Handler_Models
{
    /**
     * Check if model has to be saved. Using deprecated in php5.2 'is_a' which should
     * be restored in php 5.3. So you may remove '@' if you use php 5.3 or higher.
     *
     * If the model gets into registry, it will be caught on post-dispatch and logged
     *
     * @param Mage_Core_Model_Abstract $model
     * @param Varien_Simplexml_Element $config
     */
    public function saveAfterGeneric($model, $config)
    {
        $fullActionName = $config->getName();
        if ($config->expected_model
            && ($className = Mage::getConfig()->getModelClassName((string)$config->expected_model))
            && ($model instanceof $className)) {
            if ((string)$config->allow_model_repeat != 0) {
                if (!Mage::registry("enterprise_logging_saved_model_{$fullActionName}")) {
                    Mage::register("enterprise_logging_saved_model_{$fullActionName}", $model);
                }
            }
            else {
                Mage::register("enterprise_logging_saved_model_{$fullActionName}", $model);
            }
        }
    }

    /**
     * Orders status history update handler
     *
     * @param Mage_Core_Model_Abstract
     * @param Varien_Simplexml_Element $config
     */
    public function orderStatusHistorySaveAfter($model, $config)
    {
        if (($model instanceof Mage_Sales_Model_Order_Status_History)
            && !Mage::registry('enterprise_logging_saved_model_adminhtml_sales_order_addComment')) {
            Mage::register('enterprise_logging_saved_model_adminhtml_sales_order_addComment', $model);
        }
    }
}
