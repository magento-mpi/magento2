<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
