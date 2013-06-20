<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Available theme list
 *
 * @method int getNextPage()
 * @method Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Available setNextPage(int $page)
 */
class Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Available
    extends Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
{
    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Available Themes');
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
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return $this
     */
    protected function _addEditButtonHtml($themeBlock)
    {
        $themeId = $themeBlock->getTheme()->getId();

        /** @var $assignButton Mage_Backend_Block_Widget_Button */
        $assignButton = $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button');
        $assignButton->setData(array(
            'label' => $this->__('Edit'),
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
     * @param Mage_DesignEditor_Block_Adminhtml_Theme $themeBlock
     * @return Mage_DesignEditor_Block_Adminhtml_Theme_Selector_List_Abstract
     */
    protected function _addThemeButtons($themeBlock)
    {
        parent::_addThemeButtons($themeBlock);
        $this->_addAssignButtonHtml($themeBlock);
        $this->_addEditButtonHtml($themeBlock);
        return $this;
    }
}
