<?php
/**
 * String length validator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Validator;

class StringLength extends \Zend_Validate_StringLength implements \Magento\Framework\Validator\ValidatorInterface
{
    /**
     * @var string
     */
    protected $_encoding = 'UTF-8';
}
