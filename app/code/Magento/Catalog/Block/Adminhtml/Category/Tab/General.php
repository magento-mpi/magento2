<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category edit general tab
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Category\Tab;

class General extends \Magento\Catalog\Block\Adminhtml\Form
{
    /**
     * @var array|null
     */
    protected $_category;

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * @return array|null
     */
    public function getCategory()
    {
        if (!$this->_category) {
            $this->_category = $this->_coreRegistry->registry('category');
        }
        return $this->_category;
    }

    /**
     * @return void
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_general');
        $form->setDataObject($this->getCategory());

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('General Information')));

        if (!$this->getCategory()->getId()) {
            //            $fieldset->addField('path', 'select', array(
            //                'name'  => 'path',
            //                'label' => __('Parent Category'),
            //                'value' => base64_decode($this->getRequest()->getParam('parent')),
            //                'values'=> $this->_getParentCategoryOptions(),
            //                //'required' => true,
            //                //'class' => 'required-entry'
            //                ),
            //                'name'
            //            );
            $parentId = $this->getRequest()->getParam('parent');
            if (!$parentId) {
                $parentId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
            }
            $fieldset->addField('path', 'hidden', array('name' => 'path', 'value' => $parentId));
        } else {
            $fieldset->addField('id', 'hidden', array('name' => 'id', 'value' => $this->getCategory()->getId()));
            $fieldset->addField('path', 'hidden', array('name' => 'path', 'value' => $this->getCategory()->getPath()));
        }

        $this->_setFieldset($this->getCategory()->getAttributes(true), $fieldset);

        if ($this->getCategory()->getId()) {
            if ($this->getCategory()->getLevel() == 1) {
                $fieldset->removeField('url_key');
                $fieldset->addField(
                    'url_key',
                    'hidden',
                    array('name' => 'url_key', 'value' => $this->getCategory()->getUrlKey())
                );
            }
        }

        $form->addValues($this->getCategory()->getData());

        $form->setFieldNameSuffix('general');
        $this->setForm($form);
    }

    /**
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array('image' => 'Magento\Catalog\Block\Adminhtml\Category\Helper\Image');
    }

    /**
     * @param array|null $node
     * @param array &$options
     * @return array
     */
    protected function _getParentCategoryOptions($node = null, &$options = array())
    {
        if (is_null($node)) {
            $node = $this->getRoot();
        }

        if ($node) {
            $options[] = array(
                'value' => $node->getPathId(),
                'label' => str_repeat('&nbsp;', max(0, 3 * $node->getLevel())) . $this->escapeHtml($node->getName())
            );

            foreach ($node->getChildren() as $child) {
                $this->_getParentCategoryOptions($child, $options);
            }
        }
        return $options;
    }
}
