<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

/**
 * Class OrderEmail
 */
interface OrderEmailInterface
{
    /**
     * Invoke OrderEmail service
     *
     * @param int $id
     * @return bool
     */
    public function invoke($id);
}
