<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Mage_Core_Controller_FrontInterface
{
    /**
     * Dispatch request and send response
     *
     * @return Mage_Core_Controller_FrontInterface
     */
    public function dispatch();
}
