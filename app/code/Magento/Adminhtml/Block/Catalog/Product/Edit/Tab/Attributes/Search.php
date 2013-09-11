<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * New attribute panel on product edit page
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab\Attributes;

class Search extends \Magento\Backend\Block\Widget
{
    /**
     * Define block template
     */
    protected function _construct()
    {
        $this->setTemplate('Magento_Catalog::product/edit/attribute/search.phtml');
        parent::_construct();
    }

    /**
     * @return array
     */
    public function getSelectorOptions()
    {
        $templateId = \Mage::registry('product')->getAttributeSetId();
        return array(
            'source' => $this->getUrl('*/catalog_product/suggestAttributes'),
            'minLength' => 0,
            'ajaxOptions' => array('data' => array('template_id' => $templateId)),
            'template' => '[data-template-for="product-attribute-search"]',
            'data' => $this->getSuggestedAttributes('', $templateId),
        );
    }

    /**
     * Retrieve list of attributes with admin store label containing $labelPart
     *
     * @param string $labelPart
     * @param int $templateId
     * @return \Magento\Catalog\Model\Resource\Product\Attribute\Collection
     */
    public function getSuggestedAttributes($labelPart, $templateId = null)
    {
        $escapedLabelPart = \Mage::getResourceHelper('Magento_Core')
            ->addLikeEscape($labelPart, array('position' => 'any'));
        /** @var $collection \Magento\Catalog\Model\Resource\Product\Attribute\Collection */
        $collection = \Mage::getResourceModel('Magento\Catalog\Model\Resource\Product\Attribute\Collection')
            ->addFieldToFilter('frontend_label', array('like' => $escapedLabelPart));

        $collection->setExcludeSetFilter($templateId ?: $this->getRequest()->getParam('template_id'))->setPageSize(20);

        $result = array();
        foreach ($collection->getItems() as $attribute) {
            /** @var $attribute \Magento\Catalog\Model\Resource\Eav\Attribute */
            $result[] = array(
                'id'      => $attribute->getId(),
                'label'   => $attribute->getFrontendLabel(),
                'code'    => $attribute->getAttributeCode(),
            );
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getAddAttributeUrl()
    {
        return $this->getUrl('*/catalog_product/addAttributeToTemplate');
    }
}
