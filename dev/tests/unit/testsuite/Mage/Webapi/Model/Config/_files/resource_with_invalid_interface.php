<?php
/**
 * Tests fixture for Auto Discovery functionality.
 *
 * Fake resource controller with invalid interface.
 *
 * @copyright {}
 */
class Vendor_Module_Webapi_Resource_InvalidController
{
    /**
     * @param int $resourceId
     */
    public function updateV1($resourceId)
    {
        // Body is intentionally left empty
    }

    public function updateV2()
    {
        // Body is intentionally left empty
    }
}
