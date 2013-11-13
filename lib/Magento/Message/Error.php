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
     * @var string
     */
    protected $type = \Magento\Message\Factory::ERROR;
}
