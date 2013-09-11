<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Validation EAV entity via EAV attributes' backend models
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Model\Validator\Attribute;

class Backend extends \Magento\Validator\ValidatorAbstract
{
    /**
     * Returns true if and only if $value meets the validation requirements.
     *
     * @param \Magento\Core\Model\AbstractModel $entity
     * @return boolean
     * @throws \InvalidArgumentException
     */
    public function isValid($entity)
    {
        $this->_messages = array();
        if (!($entity instanceof \Magento\Core\Model\AbstractModel)) {
            throw new \InvalidArgumentException('Model must be extended from \Magento\Core\Model\AbstractModel');
        }
        /** @var \Magento\Eav\Model\Entity\AbstractEntity $resource */
        $resource = $entity->getResource();
        if (!($resource instanceof \Magento\Eav\Model\Entity\AbstractEntity)) {
            throw new \InvalidArgumentException('Model resource must be extended from \Magento\Eav\Model\Entity\AbstractEntity');
        }
        $resource->loadAllAttributes($entity);
        $attributes = $resource->getAttributesByCode();
        /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
        foreach ($attributes as $attribute) {
            $backend = $attribute->getBackend();
            if (!method_exists($backend, 'validate')) {
                continue;
            }
            try {
                $result = $backend->validate($entity);
                if (false === $result) {
                    $this->_messages[$attribute->getAttributeCode()][] =
                        __('The value of attribute "%1" is invalid',
                            $attribute->getAttributeCode());
                } elseif (is_string($result)) {
                    $this->_messages[$attribute->getAttributeCode()][] = $result;
                }
            } catch (\Magento\Core\Exception $e) {
                $this->_messages[$attribute->getAttributeCode()][] = $e->getMessage();
            }
        }
        return 0 == count($this->_messages);
    }
}
