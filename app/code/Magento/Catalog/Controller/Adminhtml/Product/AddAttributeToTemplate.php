<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Product;

class AddAttributeToTemplate extends \Magento\Catalog\Controller\Adminhtml\Product
{
    /**
     * Add attribute to product template
     *
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        try {
            /** @var \Magento\Eav\Model\Entity\Attribute $attribute */
            $attribute = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute')
                ->load($request->getParam('attribute_id'));

            $attributeSet = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
                ->load($request->getParam('template_id'));

            /** @var \Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection $attributeGroupCollection */
            $attributeGroupCollection = $this->_objectManager->get(
                'Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection'
            );
            $attributeGroupCollection->setAttributeSetFilter($attributeSet->getId());
            $attributeGroupCollection->addFilter('attribute_group_code', $request->getParam('group'));
            $attributeGroupCollection->setPageSize(1);

            $attributeGroup = $attributeGroupCollection->getFirstItem();

            $attribute->setAttributeSetId($attributeSet->getId())->loadEntityAttributeIdBySet();

            $attribute->setEntityTypeId($attributeSet->getEntityTypeId())
                ->setAttributeSetId($request->getParam('template_id'))
                ->setAttributeGroupId($attributeGroup->getId())
                ->setSortOrder('0')
                ->save();

            $this->getResponse()->representJson($attribute->toJson());
        } catch (\Exception $e) {
            $response = new \Magento\Framework\Object();
            $response->setError(false);
            $response->setMessage($e->getMessage());
            $this->getResponse()->representJson($response->toJson());
        }
    }
}
