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
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create Form
     *
     * @param string $entityType
     * @param string $formCode
     * @param array $attributeValues Key is attribute code.
     * @param bool $isAjax
     * @param bool $ignoreInvisible
     * @param array $filterAttributes
     * @return \Magento\Customer\Model\Metadata\Form
     */
    public function create(
        $entityType,
        $formCode,
        array $attributeValues = array(),
        $isAjax = false,
        $ignoreInvisible = Form::IGNORE_INVISIBLE,
        $filterAttributes = array()
    ) {
        $params = array(
            'entityType' => $entityType,
            'formCode' => $formCode,
            'attributeValues' => $attributeValues,
            'ignoreInvisible' => $ignoreInvisible,
            'filterAttributes' => $filterAttributes,
            'isAjax' => $isAjax
        );
        return $this->_objectManager->create('Magento\Customer\Model\Metadata\Form', $params);
    }
}
