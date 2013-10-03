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

class Helper extends \Magento\Core\Model\Resource\Helper
{
    /**
     * Construct
     *
     * @param \Magento\Core\Model\Resource $resource
     * @param string $modulePrefix
     */
    public function __construct(
        \Magento\Core\Model\Resource $resource,
        $modulePrefix = 'Logging'
    ) {
        parent::__construct($resource, $modulePrefix);
    }
}
