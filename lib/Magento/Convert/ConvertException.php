<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Convert
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Convert exception
 *
 * @category   Magento
 * @package    Magento_Convert
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Convert;

class ConvertException extends \Magento\MagentoException
{
    const NOTICE = 'NOTICE';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const FATAL = 'FATAL';

    protected $_container;

    protected $_level;

    protected $_position;

    public function setContainer($container)
    {
        $this->_container = $container;
        return $this;
    }

    public function getContainer()
    {
        return $this->_container;
    }

    public function getLevel()
    {
        return $this->_level;
    }

    public function setLevel($level)
    {
        $this->_level = $level;
        return $this;
    }

    public function getPosition()
    {
        return $this->_position;
    }

    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }
}
