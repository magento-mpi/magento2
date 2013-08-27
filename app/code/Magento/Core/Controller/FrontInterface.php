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
     * Initialize front controller
     *
     * @return Magento_Core_Controller_FrontInterface
     */
    public function init();

    /**
     * Dispatch request and send response
     *
     * @return Magento_Core_Controller_FrontInterface
     */
    public function dispatch();
}
