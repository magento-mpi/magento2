<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Core\Model\Message;

class Warning extends \Magento\Core\Model\Message\AbstractMessage
{
    /**
     * @param string $code
     */
    public function __construct($code)
    {
        parent::__construct(\Magento\Core\Model\Message::WARNING, $code);
    }
}
