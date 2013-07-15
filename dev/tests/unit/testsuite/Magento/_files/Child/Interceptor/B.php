<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Test_Di_Child_Interceptor_B
{
    /**
     * @param string $param
     * @return string
     */
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
