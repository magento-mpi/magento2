<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Test_Di_Child_Interceptor_B
{
    public function wrapBefore($param)
    {
        return 'B' . $param . 'B';
    }

    /**
     * @param string $returnValue
     * @return string
     */
    public function wrapAfter($returnValue)
    {
        return '_B_' . $returnValue . '_B_';
    }
}
