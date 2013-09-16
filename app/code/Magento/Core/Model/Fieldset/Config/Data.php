<?php
/**
 * Fieldset configuration data container. Provides fieldset configuration data based on current config scope
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Fieldset_Config_Data extends Magento_Config_Data
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');
}
