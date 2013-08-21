<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Request data interface
 */
interface Magento_Backend_Model_Config_Backend_File_RequestData_Interface
{
    /**
     * Retrieve uploaded file tmp name by path
     *
     * @param string $path
     * @return string
     */
    public function getTmpName($path);

    /**
     * Retrieve uploaded file name by path
     *
     * @param string $path
     * @return string
     */
    public function getName($path);
}
