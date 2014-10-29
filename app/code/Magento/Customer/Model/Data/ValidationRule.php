<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Model\Data;

class ValidationRule extends \Magento\Framework\Service\Data\AbstractExtensibleObject implements
    \Magento\Customer\Api\Data\ValidationRuleInterface
{
    /**#@+
     * Constants for keys of data array
     */
    const NAME = 'name';

    const VALUE = 'value';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }
}
