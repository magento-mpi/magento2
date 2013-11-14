<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Notice message model
 */
class Notice extends AbstractMessage
{
    /**
     * @var string
     */
    protected $type = Factory::NOTICE;
}
