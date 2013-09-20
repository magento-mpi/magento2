<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_ImportExport_Model_Export_ConfigInterface
{
    /**
     * Retrieve import entities configuration
     *
     * @return array
     */
    public function getEntities();

    /**
     * Retrieve export file formats configuration
     *
     * @return array
     */
    public function getFileFormats();

    /**
     * Retrieve import product types configuration
     *
     * @return array
     */
    public function getProductTypes();
}
