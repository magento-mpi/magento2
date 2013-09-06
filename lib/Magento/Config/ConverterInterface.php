<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Config_ConverterInterface
{
    /**
     * Convert config
     *
     * @param mixed $source
     * @param array
     */
    public function convert($source);
}
