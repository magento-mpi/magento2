<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit button block
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar\Buttons;

class Edit
    extends \Magento\Backend\Block\Widget\Button\SplitButton
{
    /**
     * @var \Magento\DesignEditor\Model\Theme\Context
     */
    protected $_themeContext;

    /**
     * @var \Magento\DesignEditor\Model\Theme\ChangeFactory
     */
    protected $_changeFactory;

    /**
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_localeModel;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\DesignEditor\Model\Theme\Context $themeContext
     * @param \Magento\DesignEditor\Model\Theme\ChangeFactory $changeFactory
     * @param \Magento\Core\Model\LocaleInterface $localeModel
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\DesignEditor\Model\Theme\Context $themeContext,
        \Magento\DesignEditor\Model\Theme\ChangeFactory $changeFactory,
        \Magento\Core\Model\LocaleInterface $localeModel,
        array $data = array()
    ) {
        $this->_themeContext = $themeContext;
        $this->_changeFactory = $changeFactory;
        $this->_localeModel = $localeModel;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Init edit button
     *
     * @return $this
     */
    public function init()
    {
        $this->_initEditButton();
        return $this;
    }

    /**
     * Retrieve options attributes html
     *
     * @param string $key
     * @param array $option
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getOptionAttributesHtml($key, $option)
    {
        $disabled = isset($option['disabled']) && $option['disabled'] ? 'disabled' : '';
        $title = isset($option['title']) ? $option['title'] : $option['label'];

        $classes = array();
        $classes[] = 'vde_cell_list_item';
        if (!empty($option['default'])) {
            $classes[] = 'checked';
        }
        if ($disabled) {
            $classes[] = $disabled;
        }

        $attributes = $this->_prepareOptionAttributes($option, $title, $classes, $disabled);
        $html = $this->_getAttributesString($attributes);
        $html .= $this->getUiId(isset($option['id']) ? $option['id'] : 'item' . '-' . $key);

        return $html;
    }

    /**
     * Whether button is disabled
     *
     * @return mixed
     */
    public function getDisabled()
    {
        return false;
    }

    /**
     * Disable actions-split functionality if no options provided
     *
     * @return bool
     */
    public function hasSplit()
    {
        $options = $this->getOptions();
        return is_array($options) && count($options) > 0;
    }

    /**
     * Get URL to apply changes from 'staging' theme to 'virtual' theme
     *
     * @param string $revertType
     * @return string
     */
    public function getRevertUrl($revertType)
    {
        return $this->getUrl('adminhtml/system_design_editor/revert', array(
            'theme_id'  => $this->_themeContext->getEditableTheme()->getId(),
            'revert_to' => $revertType
        ));
    }

    /**
     * Init 'Edit' button for 'physical' theme
     *
     * @return $this
     */
    protected function _initEditButton()
    {
        $isPhysicalTheme = $this->_themeContext->getEditableTheme()->isPhysical();
        $this->setData(array(
            'label'          => __('Edit'),
            'options'        => array(
                array(
                    'label'          => __('Restore last saved version of theme'),
                    'data_attribute' => array('mage-init' => $this->_getDataRevertToPrevious()),
                    'disabled'       => $isPhysicalTheme || !$this->_isAbleRevertToPrevious()
                ),
                array(
                    'label'          => __('Restore theme defaults'),
                    'data_attribute' => array('mage-init' => $this->_getDataRevertToDefault()),
                    'disabled'       => $isPhysicalTheme || !$this->_isAbleRevertToDefault()
                )
            )
        ));

        return $this;
    }

    /**
     * Get json options for button (restore-to-previous)
     *
     * @return string|bool
     */
    protected function _getDataRevertToPrevious()
    {
        $sourceChange = $this->_changeFactory->create();
        $sourceChange->loadByThemeId($this->_themeContext->getEditableTheme()->getId());
        $dateMessage = $this->_localeModel
            ->date($sourceChange->getChangeTime(), \Magento\Stdlib\DateTime::DATETIME_INTERNAL_FORMAT)->toString();
        $message = __('Do you want to restore the version saved at %1?', $dateMessage);

        $data = array(
            'vde-edit-button' => array(
                'event'     => 'revert-to-last',
                'target'    => 'body',
                'eventData' => array(
                    'url'     => $this->getRevertUrl('last_saved'),
                    'confirm' => array('title' => __('Restore Theme Version'), 'message' => $message),
                )
            )
        );
        return $this->escapeHtml(json_encode($data));
    }

    /**
     * Get json options for button (restore-to-default)
     *
     * @return string|bool
     */
    protected function _getDataRevertToDefault()
    {
        $message = __('Do you want to restore the theme defaults?');
        $data = array(
            'vde-edit-button' => array(
                'event'     => 'revert-to-default',
                'target'    => 'body',
                'eventData' => array(
                    'url'     => $this->getRevertUrl('physical'),
                    'confirm' => array('title' => __('Restore Theme Defaults'), 'message' => $message)
                )
            )
        );
        return $this->escapeHtml(json_encode($data));
    }

    /**
     * Check themes by change time (compare staging and virtual theme)
     *
     * @return bool
     */
    protected function _isAbleRevertToPrevious()
    {
        return $this->_hasThemeChanged(
            $this->_themeContext->getStagingTheme(),
            $this->_themeContext->getEditableTheme()
        );
    }

    /**
     * Check themes by change time (compare staging and physical theme)
     *
     * @return bool
     */
    protected function _isAbleRevertToDefault()
    {
        return $this->_hasThemeChanged(
            $this->_themeContext->getStagingTheme(),
            $this->_themeContext->getEditableTheme()->getParentTheme()
        );
    }

    /**
     * Checks themes for changes by time
     *
     * @param \Magento\View\Design\ThemeInterface $sourceTheme
     * @param \Magento\View\Design\ThemeInterface $targetTheme
     * @return bool
     */
    protected function _hasThemeChanged(\Magento\View\Design\ThemeInterface $sourceTheme, \Magento\View\Design\ThemeInterface $targetTheme)
    {
        $sourceChange = $this->_changeFactory->create();
        $sourceChange->loadByThemeId($sourceTheme->getId());

        $targetChange = $this->_changeFactory->create();
        $targetChange->loadByThemeId($targetTheme->getId());

        return $sourceChange->getChangeTime() !== $targetChange->getChangeTime();
    }
}
