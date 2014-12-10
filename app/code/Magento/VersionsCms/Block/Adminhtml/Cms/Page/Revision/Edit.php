<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision;

/**
 * Edit revision page
 */
class Edit extends \Magento\Cms\Block\Adminhtml\Page\Edit
{
    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        array $data = array()
    ) {
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Constructor. Modifying default CE buttons.
     *
     * @return $this
     */
    protected function _construct()
    {
        parent::_construct();

        $this->removeButton('delete');

        $this->_objectId = 'revision_id';

        $this->_controller = 'adminhtml_cms_page_revision';
        $this->_blockGroup = 'Magento_VersionsCms';

        $this->setFormActionUrl($this->getUrl('adminhtml/cms_page_revision/save'));

        $objId = $this->getRequest()->getParam($this->_objectId);

        if (!empty($objId) && $this->_cmsConfig->canCurrentUserDeleteRevision()) {
            $this->buttonList->add(
                'delete_revision',
                array(
                    'label' => __('Delete'),
                    'class' => 'delete',
                    'onclick' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to delete this revision?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')'
                )
            );
        }

        $this->buttonList->add(
            'preview',
            array(
                'label' => __('Preview'),
                'class' => 'preview',
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array(
                            'event' => 'preview',
                            'target' => '#edit_form',
                            'eventData' => array('action' => $this->getPreviewUrl())
                        )
                    )
                )
            )
        );

        if ($this->_cmsConfig->canCurrentUserPublishRevision()) {
            $this->buttonList->add(
                'publish',
                array(
                    'id' => 'publish_button',
                    'label' => __('Publish'),
                    'onclick' => "publishAction('" . $this->getPublishUrl() . "')",
                    'class' => 'publish' . ($this->_coreRegistry->registry('cms_page')->getId() ? '' : ' no-display')
                ),
                1
            );

            if ($this->_cmsConfig->canCurrentUserSaveRevision()) {
                $this->buttonList->add(
                    'save_publish',
                    array(
                        'id' => 'save_publish_button',
                        'label' => __('Save and publish.'),
                        'class' => 'publish no-display',
                        'data_attribute' => array(
                            'mage-init' => array(
                                'button' => array('event' => 'saveAndPublish', 'target' => '#edit_form')
                            )
                        )
                    ),
                    1
                );
            }

            $this->buttonList->update('saveandcontinue', 'level', 2);
        }

        if ($this->_cmsConfig->canCurrentUserSaveRevision()) {
            $this->buttonList->update('save', 'label', __('Save'));
            $this->buttonList->update(
                'save',
                'data_attribute',
                array('mage-init' => array('button' => array('event' => 'save', 'target' => '#edit_form')))
            );
            $this->buttonList->update(
                'saveandcontinue',
                'data_attribute',
                array('mage-init' => array('button' => array('event' => 'preview', 'target' => '#edit_form')))
            );

            $page = $this->_coreRegistry->registry('cms_page');
            // Adding button to create new version
            $this->buttonList->add(
                'new_version',
                array(
                    'id' => 'new_version',
                    'label' => __('Save in a new version.'),
                    'data_attribute' => array(
                        'mage-init' => array(
                            'button' => array(
                                'event' => 'save',
                                'target' => '#edit_form',
                                'eventData' => array(
                                    'action' => $this->getNewVersionUrl(),
                                    'target' => 'cms-page-preview-' . ($page ? $page->getId() : '')
                                )
                            )
                        )
                    ),
                    'class' => 'new'
                )
            );

            $this->_formScripts[] = "
                function newVersionAction(e){
                    var versionName = prompt('" .
                __(
                    'You must specify a new version name.'
                ) . "', '')
                    if (versionName == '') {
                        alert('" .
                        __(
                            'Please specify a valid name.'
                        ) .
                "');
                        e.stopImmediatePropagation();
                    } else if (versionName == null) {
                        return false;
                        e.stopImmediatePropagation();
                    }
                    $('page_label').value = versionName;
                }
                (function($){
                    $('#new_version').on('click', newVersionAction);
                })(jQuery)
            ";
        } else {
            $this->removeButton('save');
            $this->removeButton('saveandcontinue');
        }

        return $this;
    }

    /**
     * Retrieve text for header element depending
     * on loaded page and revision
     *
     * @return string
     */
    public function getHeaderText()
    {
        $revisionNumber = $this->_coreRegistry->registry('cms_page')->getRevisionNumber();
        $title = $this->escapeHtml($this->_coreRegistry->registry('cms_page')->getTitle());

        if ($revisionNumber) {
            return __("Edit Page '%1' Revision #%2", $title, $this->escapeHtml($revisionNumber));
        } else {
            return __("Edit Page '%1' New Revision", $title);
        }
    }

    /**
     * Check permission for passed action
     * Rewrite CE save permission to EE save_revision
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        if ($action == 'Magento_Cms::save') {
            $action = 'Magento_VersionsCms::save_revision';
        }
        return parent::_isAllowedAction($action);
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $page = $this->_coreRegistry->registry('cms_page');
        return $this->getUrl(
            'adminhtml/cms_page_version/edit',
            array('page_id' => $page ? $page->getId() : null, 'version_id' => $page ? $page->getVersionId() : null)
        );
    }

    /**
     * Get URL for delete button
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('adminhtml/*/delete', array('_current' => true));
    }

    /**
     * Get URL for publish button
     *
     * @return string
     */
    public function getPublishUrl()
    {
        return $this->getUrl('adminhtml/*/publish', array('_current' => true));
    }

    /**
     * Get URL for preview button
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('adminhtml/*/preview');
    }

    /**
     * Adding info block html before form html
     *
     * @return string
     */
    public function getFormHtml()
    {
        return $this->getChildHtml('revision_info') . parent::getFormHtml();
    }

    /**
     * Save into new version link
     *
     * @return string
     */
    public function getNewVersionUrl()
    {
        return $this->getUrl('adminhtml/cms_page_version/new');
    }
}
