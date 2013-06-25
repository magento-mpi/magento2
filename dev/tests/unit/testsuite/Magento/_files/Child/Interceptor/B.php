<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Test_Di_Interceptor_B
{
    /**
     * @param string $returnValue
     * @return string
     */
    public function wrapAfter($returnValue)
    {
        return '\'' . $returnValue . '\'';
    }
}
