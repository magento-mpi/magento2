<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;


/**
 * Class OrderNotifyUser
 */
Interface OrderNotifyUserInterface
{
    /**
     * Invoke notifyUser service
     *
     * @param int $id
     * @return \Magento\Framework\Service\Data\AbstractObject
     */
    public function invoke($id);
}
