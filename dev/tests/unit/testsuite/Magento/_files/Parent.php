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
     * @var string
     */
    protected $_wrapperSymbol;

    /**
     * @param string $wrapperSymbol
     */
    public function __construct($wrapperSymbol = '|')
    {
        $this->_wrapperSymbol = $wrapperSymbol;
    }

    /**
     * @param string $param
     * @return mixed
     */
    public function wrap($param)
    {
        return $this->_wrapperSymbol . $param . $this->_wrapperSymbol;
    }
}
