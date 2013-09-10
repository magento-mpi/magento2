<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Core_Model_Config_DataInterface
{
    /**
     * @param $path
     * @return mixed
     */
    public function getValue($path);
}
