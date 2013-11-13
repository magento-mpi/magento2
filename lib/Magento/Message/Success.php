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
     * @var string
     */
    protected $type = \Magento\Message\Factory::SUCCESS;
}
