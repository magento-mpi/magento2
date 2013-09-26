<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ImportExport\Model\Import;

interface ConfigInterface
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
