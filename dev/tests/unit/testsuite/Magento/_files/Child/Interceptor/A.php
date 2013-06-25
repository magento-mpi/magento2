<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Test_Di_Interceptor_A
{
    public function wrapBefore($param)
    {
        return '"' . $param . '"';
    }
}
