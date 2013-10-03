<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Controller;

interface FrontInterface
{
    /**
     * Dispatch request and send response
     *
     * @return \Magento\Core\Controller\FrontInterface
     */
    public function dispatch();
}
