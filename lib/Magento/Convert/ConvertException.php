<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Convert
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Convert;
use Magento\Convert\Container\AbstractContainer;
use Magento\Exception;

/**
 * Convert exception
 */
class ConvertException extends Exception
{
    const NOTICE = 'NOTICE';
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';
    const FATAL = 'FATAL';

    /**
     * @var AbstractContainer
     */
    protected $_container;

    /**
     * @var string
     */
    protected $_level;

    /**
     * @var int
     */
    protected $_position;

    /**
     * @param AbstractContainer $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * @return AbstractContainer
     */
    public function getContainer()
    {
        return $this->_container;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->_level;
    }

    /**
     * @param string $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->_level = $level;
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * @param int $position
     * @return $this
     */
    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }
}
