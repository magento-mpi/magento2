<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sitemap\Block\Adminhtml\Edit;

/**
 * Sitemap edit form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Core\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Core\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Core\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sitemap_form');
        $this->setTitle(__('Sitemap Information'));
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('sitemap_sitemap');

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'data' => array(
                'id'        => 'edit_form',
                'action'    => $this->getData('action'),
                'method'    => 'post',
            ))
        );

        $fieldset = $form->addFieldset('add_sitemap_form', array('legend' => __('Sitemap')));

        if ($model->getId()) {
            $fieldset->addField('sitemap_id', 'hidden', array(
                'name' => 'sitemap_id',
            ));
        }

        $fieldset->addField('sitemap_filename', 'text', array(
            'label' => __('Filename'),
            'name'  => 'sitemap_filename',
            'required' => true,
            'note'  => __('example: sitemap.xml'),
            'value' => $model->getSitemapFilename()
        ));

        $fieldset->addField('sitemap_path', 'text', array(
            'label' => __('Path'),
            'name'  => 'sitemap_path',
            'required' => true,
            'note'  => __('example: "sitemap/" or "/" for base path (path must be writeable)'),
            'value' => $model->getSitemapPath()
        ));

        if (!$this->_storeManager->hasSingleStore()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label'    => __('Store View'),
                'title'    => __('Store View'),
                'name'     => 'store_id',
                'required' => true,
                'value'    => $model->getStoreId(),
                'values'   => $this->_systemStore->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'     => 'store_id',
                'value'    => $this->_storeManager->getStore(true)->getId()
            ));
            $model->setStoreId($this->_storeManager->getStore(true)->getId());
        }

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
