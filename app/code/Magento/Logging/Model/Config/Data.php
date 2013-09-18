<?php
/**
 * Logging configuration data container.
 *
 * Provides fieldset configuration data based on current config scope
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Logging\Model\Config;

class Data extends \Magento\Config\Data
{
    /**
     * Scope priority loading scheme
     *
     * @var array
     */
    protected $_scopePriorityScheme = array('global');
}
