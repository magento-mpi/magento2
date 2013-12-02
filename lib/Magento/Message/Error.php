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
class Error extends AbstractMessage
{
    /**
     * @var string
     */
    protected $type = Factory::ERROR;
}
