<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Success message model
 */
class Success extends \Magento\Message\AbstractMessage
{
    /**
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(\Magento\Message\Factory::SUCCESS, $code);
    }
}
