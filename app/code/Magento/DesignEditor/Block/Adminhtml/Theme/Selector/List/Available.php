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
 * Available theme list
 *
 * @method int getNextPage()
 * @method Magento_DesignEditor_Block_Adminhtml_Theme_Selector_List_Available setNextPage(int $page)
 */
class Magento_DesignEditor_Block_Adminhtml_Theme_Selector_List_Available
    extends Magento_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
{
    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Available Themes');
    }

    /**
     * Get next page url
     *
     * @return string
     */
    public function getNextPageUrl()
    {
        return $this->getNextPage() <= $this->getCollection()->getLastPageNumber()
            ? $this->getUrl('*/*/*', array('page' => $this->getNextPage()))
            : '';
    }

    /**
     * Get edit button
     *
     * @param Magento_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return $this
     */
    protected function _addEditButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();

        /** @var $assignButton Magento_Backend_Block_Widget_Button */
        $assignButton = $this->getLayout()->createBlock('Magento_Backend_Block_Widget_Button');
        $assignButton->setData(array(
            'label' => __('Edit'),
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'themeEdit',
                        'target' => 'body',
                        'eventData' => array(
                            'theme_id' => $themeId
                        )
                    ),
                ),
            ),
            'class' => 'action-edit',
        ));

        $themeBlock->addButton($assignButton);
    }

    /**
     * Add theme buttons
     *
     * @param Magento_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Magento_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
     */
    protected function _addThemeButtons($themeBlock)
    {
        parent::_addThemeButtons($themeBlock);
        $this->_addAssignButtonHtml($themeBlock);
        $this->_addEditButtonHtml($themeBlock);
        return $this;
    }
}
