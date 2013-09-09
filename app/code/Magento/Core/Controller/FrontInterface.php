<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Core_Controller_FrontInterface
{
    /**
     * Dispatch request and send response
     *
     * @return Magento_Core_Controller_FrontInterface
     */
    public function dispatch();
}
