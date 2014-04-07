<?php
/**
 * No such entity service exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Exception;

class NoSuchEntityException extends \Magento\Exception\LocalizedException
{
    /** @var int */
    protected $fieldCtr = 0;

    /**
     * @param string $message
     * @param array $params
     * @param \Exception $cause
     */
    public function __construct(
        $message = 'No such entity',
        array $params = [],
        \Exception $cause = null
    ) {
        parent::__construct($message, $params, $cause);
        if (!empty($params)) {
            $this->fieldCtr++;
        }
    }

    /**
     * @param string $fieldName name of the field searched upon
     * @param string $value     the value of the field
     * @return $this
     */
    public function addField($fieldName, $value)
    {
        $fieldKey = 'fieldName' . $this->fieldCtr;
        $valueKey = 'value' . $this->fieldCtr;
        if ($this->fieldCtr == 0) {
            $newRawMessage = ' with %' . $fieldKey . ' = %' . $valueKey;
            $this->message .= $newRawMessage;
            $this->rawMessage .= $newRawMessage;
        } else {
            $newRawMessage = "\n %" . $fieldKey . ' = %' . $valueKey;
            $this->message .= $newRawMessage;
            $this->rawMessage .= $newRawMessage;
        }
        $arguments = [
            $fieldKey => $fieldName,
            $valueKey => $value
        ];
        $this->params = array_merge($this->params, $arguments);
        $this->message = __($this->message, $arguments);
        $this->fieldCtr++;
        return $this;
    }
}
