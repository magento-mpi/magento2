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
namespace Magento\Logging\Model\Resource\Helper;

class Mysql4 extends \Magento\Core\Model\Resource\Helper\Mysql4
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
