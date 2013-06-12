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
     * @var Mage_Core_Model_Theme_Service
     */
    protected $_serviceModel;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Theme_Service $serviceModel
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Theme_Service $serviceModel,
        array $data = array()
    ) {
        $this->_serviceModel = $serviceModel;
        parent::__construct($context, $data);
    }

    /**
     * Get service model
     *
     * @return Mage_Core_Model_Theme_Service
     */
    protected function _getServiceModel()
    {
        return $this->_serviceModel;
    }

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
