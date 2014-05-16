<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Resource helper class
 */
namespace Magento\Logging\Model\Resource;

class Helper extends \Magento\Framework\DB\Helper
{
    /**
     * Construct
     *
     * @param \Magento\Framework\App\Resource $resource
     * @param string $modulePrefix
     */
    public function __construct(\Magento\Framework\App\Resource $resource, $modulePrefix = 'Logging')
    {
        parent::__construct($resource, $modulePrefix);
    }
}
