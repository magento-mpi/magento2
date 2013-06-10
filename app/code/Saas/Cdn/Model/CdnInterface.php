<?php
/**
 * CDN interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Saas_Cdn_Model_CdnInterface
{
    /**
     * Delete file from CDN
     *
     * @param string $path
     * @return boolean
     * @throws Saas_Cdn_Exception
     */
    public function deleteFile($path);

    /**
     * Delete file from CDN recursively
     *
     * @param string $path
     * @return boolean
     * @throws Saas_Cdn_Exception
     */
    public function deleteRecursively($path);
}
