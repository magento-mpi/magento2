<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Warning message model
 */
class Warning extends \Magento\Message\AbstractMessage
{
    /**
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(\Magento\Message\Factory::WARNING, $code);
    }
}
