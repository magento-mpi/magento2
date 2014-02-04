<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml;

use Magento\Backend\Block\Widget\Button;

/**
 * Design editor theme
 *
 * @method \Magento\DesignEditor\Block\Adminhtml\Theme setTheme(\Magento\View\Design\ThemeInterface $theme)
 * @method \Magento\View\Design\ThemeInterface getTheme()
 */
class Theme extends \Magento\Backend\Block\Template
{
    /**
     * Buttons array
     *
     * @var Button[]
     */
    protected $_buttons = array();

    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreHelper,
        array $data = array()
    ) {
        $this->_coreHelper = $coreHelper;
        parent::__construct($context, $data);
    }

    /**
     * Add button
     *
     * @param Button $button
     * @return \Magento\DesignEditor\Block\Adminhtml\Theme
     */
    public function addButton($button)
    {
        $this->_buttons[] = $button;
        return $this;
    }

    /**
     * Clear buttons
     *
     * @return \Magento\DesignEditor\Block\Adminhtml\Theme
     */
    public function clearButtons()
    {
        $this->_buttons = array();
        return $this;
    }

    /**
     * Get buttons html
     *
     * @return string
     */
    public function getButtonsHtml()
    {
        $output = '';
        /** @var $button Button */
        foreach ($this->_buttons as $button) {
            $output .= $button->toHtml();
        }
        return $output;
    }

    /**
     * Return array of assigned stores titles
     *
     * @return string[]
     */
    public function getStoresTitles()
    {
        $storesTitles = array();
        /** @var $store \Magento\Core\Model\Store */
        foreach ($this->getTheme()->getAssignedStores() as $store) {
            $storesTitles[] = $store->getName();
        }
        return $storesTitles;
    }

    /**
     * Get options for JS widget vde.themeControl
     *
     * @return string
     */
    public function getOptionsJson()
    {
        $theme = $this->getTheme();
        $options = array(
            'theme_id'    => $theme->getId(),
            'theme_title' => $theme->getThemeTitle()
        );

        /** @var $helper \Magento\Core\Helper\Data */
        $helper = $this->_coreHelper;
        return $helper->jsonEncode($options);
    }

    /**
     * Get quick save button
     *
     * @return Button
     */
    public function getQuickSaveButton()
    {
        /** @var $saveButton Button */
        $saveButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button');
        $saveButton->setData(array(
            'label'     => __('Save'),
            'class'     => 'action-save',
        ));
        return $saveButton;
    }
}
