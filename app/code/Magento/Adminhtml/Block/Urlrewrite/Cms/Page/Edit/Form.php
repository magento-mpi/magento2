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
 * Edit form for CMS page URL rewrites
 *
 * @method Magento_Cms_Model_Page getCmsPage()
 * @method Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form setCmsPage(Magento_Cms_Model_Page $model)
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form extends Magento_Adminhtml_Block_Urlrewrite_Edit_Form
{
    /**
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * @var Magento_Cms_Model_Page_UrlrewriteFactory
     */
    protected $_urlRewriteFactory;

    /**
     * @param Magento_Cms_Model_Page_UrlrewriteFactory $urlRewriteFactory
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param Magento_Core_Model_Source_Urlrewrite_TypesFactory $typesFactory
     * @param Magento_Core_Model_Source_Urlrewrite_OptionsFactory $optionFactory
     * @param Magento_Core_Model_Url_RewriteFactory $rewriteFactory
     * @param Magento_Core_Model_System_Store $systemStore
     * @param Magento_Backend_Model_Session $backendSession
     * @param Magento_Backend_Helper_Data $adminhtmlData
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Magento_Cms_Model_Page_UrlrewriteFactory $urlRewriteFactory,
        Magento_Cms_Model_PageFactory $pageFactory,
        Magento_Core_Model_Source_Urlrewrite_TypesFactory $typesFactory,
        Magento_Core_Model_Source_Urlrewrite_OptionsFactory $optionFactory,
        Magento_Core_Model_Url_RewriteFactory $rewriteFactory,
        Magento_Core_Model_System_Store $systemStore,
        Magento_Backend_Model_Session $backendSession,
        Magento_Backend_Helper_Data $adminhtmlData,
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->_pageFactory = $pageFactory;
        parent::__construct(
            $typesFactory, $optionFactory, $rewriteFactory, $systemStore, $backendSession, $adminhtmlData, $registry,
            $formFactory, $coreData, $context, $data
        );
    }

    /**
     * Form post init
     *
     * @param Magento_Data_Form $form
     * @return Magento_Adminhtml_Block_Urlrewrite_Cms_Page_Edit_Form
     */
    protected function _formPostInit($form)
    {
        $cmsPage = $this->_getCmsPage();
        $form->setAction(
            $this->_adminhtmlData->getUrl('*/*/save', array(
                'id'       => $this->_getModel()->getId(),
                'cms_page' => $cmsPage->getId()
            ))
        );

        // Fill id path, request path and target path elements
        /** @var $idPath Magento_Data_Form_Element_Abstract */
        $idPath = $this->getForm()->getElement('id_path');
        /** @var $requestPath Magento_Data_Form_Element_Abstract */
        $requestPath = $this->getForm()->getElement('request_path');
        /** @var $targetPath Magento_Data_Form_Element_Abstract */
        $targetPath = $this->getForm()->getElement('target_path');

        $model = $this->_getModel();
        /** @var $cmsPageUrlrewrite Magento_Cms_Model_Page_Urlrewrite */
        $cmsPageUrlrewrite = $this->_urlRewriteFactory->create();
        if (!$model->getId()) {
            $idPath->setValue($cmsPageUrlrewrite->generateIdPath($cmsPage));

            $sessionData = $this->_getSessionData();
            if (!isset($sessionData['request_path'])) {
                $requestPath->setValue($cmsPageUrlrewrite->generateRequestPath($cmsPage));
            }
            $targetPath->setValue($cmsPageUrlrewrite->generateTargetPath($cmsPage));
            $disablePaths = true;
        } else {
            $cmsPageUrlrewrite->load($this->_getModel()->getId(), 'url_rewrite_id');
            $disablePaths = $cmsPageUrlrewrite->getId() > 0;
        }
        if ($disablePaths) {
            $idPath->setData('disabled', true);
            $targetPath->setData('disabled', true);
        }

        return $this;
    }

    /**
     * Get catalog entity associated stores
     *
     * @return array
     * @throws Magento_Core_Model_Store_Exception
     */
    protected function _getEntityStores()
    {
        $cmsPage = $this->_getCmsPage();
        $entityStores = array();

        // showing websites that only associated to CMS page
        if ($this->_getCmsPage()->getId()) {
            $entityStores = (array) $cmsPage->getResource()->lookupStoreIds($cmsPage->getId());
            $this->_requireStoresFilter = !in_array(0, $entityStores);

            if (!$entityStores) {
                throw new Magento_Core_Model_Store_Exception(
                    __('Chosen cms page does not associated with any website.')
                );
            }
        }

        return $entityStores;
    }

    /**
     * Get CMS page model instance
     *
     * @return Magento_Cms_Model_Page
     */
    protected function _getCmsPage()
    {
        if (!$this->hasData('cms_page')) {
            $this->setCmsPage($this->_pageFactory->create());
        }
        return $this->getCmsPage();
    }
}
