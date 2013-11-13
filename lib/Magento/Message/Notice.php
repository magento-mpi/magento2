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
class Notice extends \Magento\Message\AbstractMessage
{
    /**
     * @var string
     */
    protected $type = \Magento\Message\Factory::NOTICE;
}
