<?php
/**
 * Session validator interface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     \Magento\Sesstion
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Session;

/**
 * Session validator interface
 */
interface ValidatorInterface
{
    /**
     * Validate session
     *
     * @param \Magento\Core\Model\Session\AbstractSession $session
     * @throws \Magento\Session\Exception
     */
    public function validate(\Magento\Core\Model\Session\AbstractSession $session);
}
