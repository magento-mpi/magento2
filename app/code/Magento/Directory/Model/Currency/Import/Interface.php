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
interface \Magento\Directory\Model\Currency\Import\Interface
{
    /**
     * Import rates
     *
     * @return \Magento\Directory\Model\Currency_Import_Abstract
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
