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
     * @var string
     */
    protected $type = \Magento\Message\Factory::WARNING;
}
