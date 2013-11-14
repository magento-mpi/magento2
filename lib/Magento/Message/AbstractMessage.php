<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Message;

/**
 * Abstract message model
 */
abstract class AbstractMessage
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var mixed
     */
    protected $class;

    /**
     * @var mixed
     */
    protected $method;

    /**
     * @var mixed
     */
    protected $identifier;

    /**
     * @var bool
     */
    protected $isSticky = false;

    /**
     * @param string $code
     */
    public function __construct($code = '')
    {
        $this->code = $code;
    }

    /**
     * Get message code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get message text
     *
     * @return string
     */
    public function getText()
    {
        return $this->getCode();
    }

    /**
     * Get message type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get message class
     *
     * @param $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Get message method
     *
     * @param $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Convert message to string
     *
     * @return string
     */
    public function toString()
    {
        $out = $this->getType() . ': ' . $this->getText();
        return $out;
    }

    /**
     * Set message identifier
     *
     * @param string $identifier
     * @return AbstractMessage
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    /**
     * Get message identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set message sticky status
     *
     * @param bool $isSticky
     * @return AbstractMessage
     */
    public function setIsSticky($isSticky = true)
    {
        $this->isSticky = $isSticky;
        return $this;
    }

    /**
     * Get whether message is sticky
     *
     * @return bool
     */
    public function getIsSticky()
    {
        return $this->isSticky;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return AbstractMessage
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }
}
