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
     * @param string $modulePrefix
     */
    public function __construct($modulePrefix = 'Logging')
    {
        parent::__construct($modulePrefix);
    }
}
