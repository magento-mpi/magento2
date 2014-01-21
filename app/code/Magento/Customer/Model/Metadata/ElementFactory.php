<?php
/**
 * Customer Form Element Factory
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata;

class ElementFactory
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Stdlib\String
     */
    protected $_string;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Stdlib\String $string
     */
    public function __construct(\Magento\ObjectManager $objectManager, \Magento\Stdlib\String $string)
    {
        $this->_objectManager = $objectManager;
        $this->_string = $string;
    }

    /**
     * Create Form Element
     *
     * @param \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute
     * @param $value
     * @param $entityTypeCode
     * @param bool $isAjax
     * @return \Magento\Customer\Model\Metadata\Form\AbstractData
     */
    public function create(
        \Magento\Customer\Service\V1\Dto\Eav\AttributeMetadata $attribute,
        $value,
        $entityTypeCode,
        $isAjax = false
    ) {
        $dataModelClass = $attribute->getDataModel();
        $params = [
            'entityTypeCode' => $entityTypeCode,
            'value' => is_null($value) ? false : $value,
            'isAjax' => $isAjax,
            'attribute' => $attribute
        ];
        /** TODO fix when Validation is implemented MAGETWO-17341 */
        if ($dataModelClass == 'Magento\Customer\Model\Attribute\Data\Postcode') {
            $dataModelClass = 'Magento\Customer\Model\Metadata\Form\Text';
        }
        if (!empty($dataModelClass)) {
            $dataModel = $this->_objectManager->create($dataModelClass, $params);
        } else {
            $dataModelClass = sprintf(
                'Magento\Customer\Model\Metadata\Form\%s',
                $this->_string->upperCaseWords($attribute->getFrontendInput())
            );
            $dataModel = $this->_objectManager->create($dataModelClass, $params);
        }

        return $dataModel;
    }
}
