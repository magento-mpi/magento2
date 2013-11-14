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
class Success extends AbstractMessage
{
    /**
     * @var string
     */
    protected $type = Factory::SUCCESS;
}
