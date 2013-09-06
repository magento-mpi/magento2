<?php
/**
 * Tests fixture for Auto Discovery functionality.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Invalid_Deprecation_Controller_Webapi_Policy
{
    /**
     * @apiDeprecated service::get
     */
    public function getV1()
    {
        // Body was intentionally left empty.
    }
}

