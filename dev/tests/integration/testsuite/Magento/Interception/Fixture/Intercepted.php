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
        return '<B>' . $param1 . $param2 . $this->C($param1) . '</B>';
    }

    public function C($param1)
    {
        return '<C>' . $param1 . '</C>';
    }

    public function D($param1)
    {
        return '<D>' . $this->_key . $param1 . '</D>';
    }

    public final function E($param1)
    {
        return '<E>' . $this->_key . $param1 . '</E>';
    }

    public function F($param1)
    {
        return '<F>' . $param1 . '</F>';
    }

    public function G($param1)
    {
        return '<G>' . $param1 . "</G>";
    }

    public function K($param1)
    {
        return '<K>' . $param1 . '</K>';
    }
}
