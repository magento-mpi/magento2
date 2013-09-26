<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_ImportExport_Model_Import_ConfigInterface
{
    /**
     * Retrieve import entities configuration
     *
     * @return array
     */
    public function getEntities();

    /**
     * Retrieve import product types configuration
     *
     * @return array
     */
    public function getProductTypes();
}
