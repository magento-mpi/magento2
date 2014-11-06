<?php
/**
 * {license_notice}
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute;

use Magento\Framework\Exception\InputException;

class OptionManagement implements \Magento\Catalog\Api\ProductAttributeOptionManagementInterface
{
    /**
     * @var \Magento\Eav\Api\AttributeOptionManagementInterface
     */
    protected $eavOptionManagement;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Eav\Api\AttributeOptionManagementInterface $eavOptionManagement
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Eav\Api\AttributeOptionManagementInterface $eavOptionManagement,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->eavOptionManagement = $eavOptionManagement;
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems($attributeCode)
    {
        return $this->eavOptionManagement->getItems(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeCode
        );
    }

    /**
     * {@inheritdoc}
     */
    public function add($attributeCode, $option)
    {
        return $this->eavOptionManagement->add(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeCode,
            $option
        );
    }

    /**
     * {@inheritdoc}
     */
    public function delete($attributeCode, $optionId)
    {
        if (empty($optionId)) {
            throw new InputException(sprintf('Invalid option id %s', $optionId));
        }

        return $this->eavOptionManagement->delete(
            \Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeCode,
            $optionId
        );
    }
}
