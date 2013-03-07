<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Di
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Tools_Di_Reporter_Console implements Tools_Di_ReporterInterface
{
    const TYPE_SUCCESS = 'success';
    const TYPE_ERROR   = 'error';

    protected $_messages = array(
        self::TYPE_SUCCESS => array(),
        self::TYPE_ERROR => array(),
    );

    public function report()
    {
        $numRepeat = 30;
        $output = PHP_EOL;
        $output .= 'SUCCESS (' . count($this->_messages[self::TYPE_SUCCESS]) . '):' . PHP_EOL;
        $output .= implode(PHP_EOL, $this->_messages[self::TYPE_SUCCESS]);
        $output .= PHP_EOL . str_repeat('-', $numRepeat) . PHP_EOL;
        $output .= 'ERROR (' . count($this->_messages[self::TYPE_ERROR]) . '):' . PHP_EOL;
        $output .= implode(PHP_EOL, $this->_messages[self::TYPE_ERROR]);
        $output .= PHP_EOL . str_repeat('-', $numRepeat) . PHP_EOL;

        echo $output;
    }

    public function addSuccess($className)
    {
        $this->_messages[self::TYPE_SUCCESS][] = $className;
    }

    public function addError($className)
    {
        $this->_messages[self::TYPE_ERROR][] = $className;
    }
}