<?php
/**
 * Tests fixture for Auto Discovery functionality.
 *
 * Fake resource controller with invalid interface.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Vendor_Module_Controller_Webapi_Invalid_Interface
{
    /**
     * @param int $resourceId
     */
    public function updateV1($resourceId)
    {
        // Body is intentionally left empty.
    }

    public function updateV2()
    {
        // Body is intentionally left empty.
    }

    public function emptyInterfaceV2()
    {
        // Body is intentionally left empty.
    }

    /**
     * @param int $resourceId
     */
    public function invalidMethodNameV2($resourceId)
    {
        // Body is intentionally left empty.
    }
}
