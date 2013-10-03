<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import currency model interface
 */
namespace Magento\Directory\Model\Currency\Import;

interface ImportInterface
{
    /**
     * Import rates
     *
     * @return \Magento\Directory\Model\Currency\Import\AbstractImport
     */
    public function importRates();

    /**
     * Fetch rates
     *
     * @return array
     */
    public function fetchRates();

    /**
     * Return messages
     *
     * @return array
     */
    public function getMessages();
}
