<?php
/**
 * Attribure lock state validator
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Attribute;

class LockValidatorComposite implements LockValidatorInterface
{
    /**
     * @var LockValidatorInterface[]
     */
    protected $validators = array();

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param array $validators
     * @throws \InvalidArgumentException
     */
    public function __construct(\Magento\ObjectManager $objectManager, array $validators = array())
    {
        foreach ($validators as $validator) {
            if (!is_subclass_of($validator, 'Magento\Catalog\Model\Attribute\LockValidatorInterface')) {
                throw new \InvalidArgumentException($validator . ' does not implements LockValidatorInterface');
            }
            $this->validators[] = $objectManager->get($validator);
        }
    }

    /**
     * Check attribute lock state
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param null $attributeSet
     * @throws \Magento\Framework\Model\Exception
     *
     * @return void
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object, $attributeSet = null)
    {
        foreach ($this->validators as $validator) {
            $validator->validate($object, $attributeSet);
        }
    }
}
