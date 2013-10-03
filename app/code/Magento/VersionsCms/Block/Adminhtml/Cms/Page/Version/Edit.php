<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit version page
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Version;

class Edit
    extends \Magento\Adminhtml\Block\Widget\Form\Container
{
    protected $_objectId   = 'version_id';
    protected $_blockGroup = 'Magento_VersionsCms';
    protected $_controller = 'adminhtml_cms_page_version';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $version = $this->_coreRegistry->registry('cms_page_version');

        // Add 'new button' depending on permission
        if ($this->_cmsConfig->canCurrentUserSaveVersion()) {
            $this->_addButton('new', array(
                    'label'     => __('Save as new version.'),
                    'class'     => 'new',
                    'data_attribute'  => array(
                        'mage-init' => array(
                            'button' => array(
                                'event' => 'save',
                                'target' => '#edit_form',
                                'eventData' => array(
                                    'action' => $this->getNewUrl()
                                )
                            ),
                        ),
                    ),
                ));

            $this->_addButton('new_revision', array(
                    'label'     => __('New Revision...'),
                    'onclick'   => "setLocation('" . $this->getNewRevisionUrl() . "');",
                    'class'     => 'new',
                ));
        }

        $isOwner = $version ? $this->_cmsConfig->isCurrentUserOwner($version->getUserId()) : false;
        $isPublisher = $this->_cmsConfig->canCurrentUserPublishRevision();

        // Only owner can remove version if he has such permissions
        if (!$isOwner || !$this->_cmsConfig->canCurrentUserDeleteVersion()) {
            $this->removeButton('delete');
        }

        // Only owner and publisher can save version
        if (($isOwner || $isPublisher) && $this->_cmsConfig->canCurrentUserSaveVersion()) {
            $this->_addButton('saveandcontinue', array(
                'label'     => __('Save and continue edit.'),
                'class'     => 'save',
                'data_attribute'  => array(
                    'mage-init' => array(
                        'button' => array(
                            'event' => 'saveAndContinueEdit', 'target' => '#edit_form'
                        ),
                    ),
                ),
            ), 1);
        } else {
            $this->removeButton('save');
        }
    }

    /**
     * Retrieve text for header element depending
     * on loaded page and version
     *
     * @return string
     */
    public function getHeaderText()
    {
        $versionLabel = $this->escapeHtml($this->_coreRegistry->registry('cms_page_version')->getLabel());
        $title = $this->escapeHtml($this->_coreRegistry->registry('cms_page')->getTitle());

        if (!$versionLabel) {
            $versionLabel = __('N/A');
        }

        return __("Edit Page '%1' Version '%2'", $title, $versionLabel);
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $cmsPage = $this->_coreRegistry->registry('cms_page');
        return $this->getUrl('*/cms_page/edit', array(
            'page_id' => $cmsPage ? $cmsPage->getPageId() : null,
            'tab' => 'versions'
        ));
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current' => true));
    }

    /**
     * Get URL for new button
     *
     * @return string
     */
    public function getNewUrl()
    {
        return $this->getUrl('*/*/new', array('_current' => true));
    }

    /**
     * Get Url for new revision button
     *
     * @return string
     */
    public function getNewRevisionUrl()
    {
        return $this->getUrl('*/cms_page_revision/new', array('_current' => true));
    }
}
