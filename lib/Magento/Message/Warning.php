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
class Warning extends AbstractMessage
{
    /**
     * @var string
     */
    protected $type = Factory::WARNING;
}
