<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Interception\Fixture;


class Intercepted extends InterceptedParent implements InterceptedInterface
{
    protected $_key;

    public function A($param1)
    {
        $this->_key = $param1;
        return $this;
    }

    public function B($param1, $param2)
    {
        return '<I_B>' . $param1 . $param2 . $this->C($param1) . '</I_B>';
    }

    public function C($param1)
    {
        return '<I_C>' . $param1 . '</I_C>';
    }

    public function D($param1)
    {
        return '<I_D>' . $this->_key . $param1 . '</I_D>';
    }

    public final function E($param1)
    {
        return '<I_E>' . $this->_key . $param1 . '</I_E>';
    }

    public function F($param1)
    {
        return '<I_F>' . $param1 . '</I_F>';
    }

    public function K($param1)
    {
        return '<I_K>' . $param1 . '</I_K>';
    }
}