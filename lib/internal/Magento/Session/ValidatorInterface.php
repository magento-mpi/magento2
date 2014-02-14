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
     * @param \Magento\Session\SessionManagerInterface $session
     * @throws \Magento\Session\Exception
     */
    public function validate(\Magento\Session\SessionManagerInterface $session);
}
