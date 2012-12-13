<?php
/**
 * String length validator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Validator_StringLength extends Zend_Validate_StringLength implements Magento_Validator_ValidatorInterface
{
    /**
     * @var string
     */
    protected $_encoding = 'UTF-8';
}
