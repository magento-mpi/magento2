<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Page\Revision;

use Magento\Backend\App\Action;
use Magento\VersionsCms\Controller\Adminhtml\Cms\Page\RevisionInterface;

class Drop extends \Magento\Backend\App\Action implements RevisionInterface
{
    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_cmsPage;

    /**
     * @var RevisionProvider
     */
    protected $revisionProvider;

    /**
     * @var \Magento\Framework\App\DesignInterface
     */
    protected $_design;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Action\Context $context
     * @param \Magento\Cms\Model\Page $page
     * @param RevisionProvider $revisionProvider
     * @param \Magento\Framework\App\DesignInterface $design
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     */
    public function __construct(
        Action\Context $context,
        \Magento\Cms\Model\Page $page,
        RevisionProvider $revisionProvider,
        \Magento\Framework\App\DesignInterface $design,
        \Magento\Framework\StoreManagerInterface $storeManager
    ) {
        $this->_cmsPage = $page;
        $this->revisionProvider = $revisionProvider;
        $this->_design = $design;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Cms::page');
    }

    /**
     * Generates preview of page. Assumed to be run in frontend area
     *
     * @return void
     */
    public function previewFrontendPage()
    {
        // check if data sent
        $data = $this->getRequest()->getPost();
        if (!empty($data) && isset($data['page_id'])) {
            // init model and set data
            $page = $this->_cmsPage->load($data['page_id']);
            if (!$page->getId()) {
                $this->_forward('noroute');
                return;
            }

            /**
             * If revision was selected load it and get data for preview from it
             */
            $_tempData = null;
            if (isset($data['preview_selected_revision']) && $data['preview_selected_revision']) {
                $revision = $this->revisionProvider->get($data['preview_selected_revision'], $this->_request);
                if ($revision->getId()) {
                    $_tempData = $revision->getData();
                }
            }

            /**
             * If there was no selected revision then use posted data
             */
            if (is_null($_tempData)) {
                $_tempData = $data;
            }

            /**
             * Posting posted data in page model
             */
            $page->addData($_tempData);

            /**
             * Retrieve store id from page model or if it was passed from post
             */
            $selectedStoreId = $page->getStoreId();
            if (is_array($selectedStoreId)) {
                $selectedStoreId = array_shift($selectedStoreId);
            }

            if (isset($data['preview_selected_store']) && $data['preview_selected_store']) {
                $selectedStoreId = $data['preview_selected_store'];
            } else {
                if (!$selectedStoreId) {
                    $selectedStoreId = $this->_storeManager->getDefaultStoreView()->getId();
                }
            }
            $selectedStoreId = (int)$selectedStoreId;

            /**
             * Emulating front environment
             */
            $this->_localeResolver->emulate($selectedStoreId);
            $this->_storeManager->setCurrentStore($this->_storeManager->getStore($selectedStoreId));

            $theme = $this->_objectManager->get(
                'Magento\Framework\View\DesignInterface'
            )->getConfigurationDesignTheme(
                null,
                array('store' => $selectedStoreId)
            );
            $this->_objectManager->get('Magento\Framework\View\DesignInterface')->setDesignTheme($theme, 'frontend');

            $designChange = $this->_design->loadChange($selectedStoreId);

            if ($designChange->getData()) {
                $this->_objectManager->get('Magento\Framework\View\DesignInterface')->setDesignTheme($designChange->getDesign());
            }

            // add handles used to render cms page on frontend
            $this->_view->getLayout()->getUpdate()->addHandle('default');
            $this->_view->getLayout()->getUpdate()->addHandle('cms_page_view');
            $this->_objectManager->get('Magento\Cms\Helper\Page')->renderPageExtended($this);
            $this->_localeResolver->revert();
        } else {
            $this->_forward('noroute');
        }
    }

    /**
     * Generates preview of page
     *
     * @return void
     */
    public function execute()
    {
        $this->_objectManager->get('Magento\Framework\Translate\Inline\StateInterface')->suspend();
        $this->_objectManager->get(
            'Magento\Framework\App\State'
        )->emulateAreaCode(
            'frontend',
            array($this, 'previewFrontendPage')
        );
    }
}
