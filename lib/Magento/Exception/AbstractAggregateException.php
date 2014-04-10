<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

abstract class AbstractAggregateException extends LocalizedException
{
    /**
     * The array of errors that have been added via the addError() method.
     *
     * @var ErrorMessage[]
     */
    protected $errors = [];

    /**
     * The original raw message passed in via the constructor
     *
     * @var string
     */
    protected $originalRawMessage;

    /**
     * The original params passed in via the constructor
     *
     * @var array
     */
    protected $originalParams = [];

    /**
     * Initialize the exception.
     *
     * @param string     $message
     * @param array      $params
     * @param \Exception $cause
     */
    public function __construct($message, array $params = [], \Exception $cause = null)
    {
        parent::__construct($message, $params, $cause);
        $this->originalRawMessage = $message;
        $this->originalParams = $params;
    }

    /**
     * Create a new error raw message object for the message and its substitution parameters.
     *
     * @param string $rawMessage Exception message
     * @param array  $params  Substitution parameters and extra error debug information
     *
     * @return $this
     */
    public function addError($rawMessage, array $params = [])
    {
        if (empty($this->errors)) {
            if ($this->rawMessage == $this->originalRawMessage && $this->params == $this->originalParams) {
                $this->rawMessage = $rawMessage;
                $this->params = $params;
            } else {
                $this->errors[] = new ErrorMessage($this->rawMessage, $this->params);
                $this->errors[] = new ErrorMessage($rawMessage, $params);
                $this->rawMessage = $this->originalRawMessage;
                $this->params = $this->originalParams;
            }
        } else {
            $this->errors[] = new ErrorMessage($rawMessage, $params);
        }
        return $this;
    }

    /**
     * Should return true if someone has added different errors to this exception after construction
     *
     * @return bool
     */
    public function hasAdditionalErrors()
    {
        if (!empty($this->errors) || $this->rawMessage != $this->originalRawMessage
            || $this->params != $this->originalParams
        ) {
            return true;
        }
        return false;
    }
    
    /**
     * Return the array of ErrorMessage objects. Return an empty array if no errors were added.
     *
     * @return ErrorMessage[]
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
