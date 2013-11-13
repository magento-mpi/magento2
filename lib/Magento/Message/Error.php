<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Error message model
 */
class Error extends \Magento\Message\AbstractMessage
{
    /**
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(\Magento\Message\Factory::ERROR, $code);
    }
}
