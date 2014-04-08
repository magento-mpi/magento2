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
    /**
     * @param string $message
     * @param array $params
     * @param \Exception $cause
     */
    public function __construct(
        $message = 'No such entity.',
        array $params = [],
        \Exception $cause = null
    ) {
        parent::__construct($message, $params, $cause);
    }

    public function addField($fieldName, $value)
    {

    }
}
