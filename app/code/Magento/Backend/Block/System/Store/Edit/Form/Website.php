<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml store edit form for website
 *
 * @category    Magento
 * @package     Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\System\Store\Edit\Form;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Website
    extends \Magento\Backend\Block\System\Store\Edit\AbstractForm
{
    /**
     * @var \Magento\Core\Model\Store\GroupFactory
     */
    protected $_groupFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Core\Model\Store\GroupFactory $groupFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Core\Model\Store\GroupFactory $groupFactory,
        array $data = array()
    ) {
        $this->_groupFactory = $groupFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare website specific fieldset
     *
     * @param \Magento\Data\Form $form
     */
    protected function _prepareStoreFieldset(\Magento\Data\Form $form)
    {
        $websiteModel = $this->_coreRegistry->registry('store_data');
        $postData = $this->_coreRegistry->registry('store_post_data');
        if ($postData) {
            $websiteModel->setData($postData['website']);
        }
        $fieldset = $form->addFieldset('website_fieldset', array(
            'legend' => __('Web Site Information')
        ));
        /* @var $fieldset \Magento\Data\Form */

        $fieldset->addField('website_name', 'text', array(
            'name'      => 'website[name]',
            'label'     => __('Name'),
            'value'     => $websiteModel->getName(),
            'required'  => true,
            'disabled'  => $websiteModel->isReadOnly(),
        ));

        $fieldset->addField('website_code', 'text', array(
            'name'      => 'website[code]',
            'label'     => __('Code'),
            'value'     => $websiteModel->getCode(),
            'required'  => true,
            'disabled'  => $websiteModel->isReadOnly(),
        ));

        $fieldset->addField('website_sort_order', 'text', array(
            'name'      => 'website[sort_order]',
            'label'     => __('Sort Order'),
            'value'     => $websiteModel->getSortOrder(),
            'required'  => false,
            'disabled'  => $websiteModel->isReadOnly(),
        ));

        if ($this->_coreRegistry->registry('store_action') == 'edit') {
            $groups = $this->_groupFactory->create()->getCollection()
                ->addWebsiteFilter($websiteModel->getId())
                ->setWithoutStoreViewFilter()
                ->toOptionArray();

            $fieldset->addField('website_default_group_id', 'select', array(
                'name'      => 'website[default_group_id]',
                'label'     => __('Default Store'),
                'value'     => $websiteModel->getDefaultGroupId(),
                'values'    => $groups,
                'required'  => false,
                'disabled'  => $websiteModel->isReadOnly(),
            ));
        }

        if (!$websiteModel->getIsDefault() && $websiteModel->getStoresCount()) {
            $fieldset->addField('is_default', 'checkbox', array(
                'name'      => 'website[is_default]',
                'label'     => __('Set as Default'),
                'value'     => 1,
                'disabled'  => $websiteModel->isReadOnly(),
            ));
        } else {
            $fieldset->addField('is_default', 'hidden', array(
                'name'      => 'website[is_default]',
                'value'     => $websiteModel->getIsDefault()
            ));
        }

        $fieldset->addField('website_website_id', 'hidden', array(
            'name'  => 'website[website_id]',
            'value' => $websiteModel->getId()
        ));
    }
}
