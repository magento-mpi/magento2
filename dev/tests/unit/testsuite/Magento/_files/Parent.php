<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Test_Di_Parent implements Magento_Test_Di_Interface
{
    /**
     * @param string $param
     * @return mixed
     */
    public function wrap($param)
    {
        return '(' . $param . ')';
    }
}
