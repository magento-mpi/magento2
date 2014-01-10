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

class NoSuchEntityException extends \Magento\Exception\Exception
{
    const NO_SUCH_ENTITY = 0;

    /**
     * @param $fieldName name of the field searched upon
     * @param $value     specific value the entity should have for it's field
     * @return self
     */
    public static function create($fieldName, $value) {
        return new self($fieldName, $value);
    }

    /**
     * @param $fieldName name of the field searched upon
     * @param $value     specific value the entity should have for it's field
     * @return self
     */
    public function __construct($fieldName, $value)
    {
        $message = "No such entity with $fieldName = $value";
        $this->_params[$fieldName] = $value;
        parent::__construct($message, self::NO_SUCH_ENTITY);
    }

    /**
     * @param $fieldName name of the field searched upon
     * @param $value     specific value the entity should have for it's field
     * @return self
     */
    public function addField($fieldName, $value) {
        $this->message .= "\n $fieldName = $value";
        $this->_params[$fieldName] = $value;
        return $this;
    }
}
