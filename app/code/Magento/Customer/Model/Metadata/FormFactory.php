<?php
/**
 * Customer Form Element Factory
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata;

class FormFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create Form
     *
     * @param string $entityType
     * @param string $formCode
     * @param array $attributeValues
     * @param bool $isAjax
     * @return \Magento\Customer\Model\Metadata\Form
     */
    public function create(
        $entityType, $formCode, $attributeValues = [], $isAjax = false
    ) {
        $params = [
            'entityType' => $entityType,
            'formCode' => $formCode,
            'attributeValues' => $attributeValues,
            'isAjax' => $isAjax,
        ];
        return $this->_objectManager->create('\Magento\Customer\Model\Metadata\Form', $params);
    }
}
