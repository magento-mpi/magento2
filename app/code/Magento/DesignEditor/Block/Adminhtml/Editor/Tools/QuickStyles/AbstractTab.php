<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\QuickStyles;

/**
 * Block that renders Quick Styles tabs
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class AbstractTab
    extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Form factory for VDE "Quick Styles" tab
     *
     * @var \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Form\Builder
     */
    protected $_formBuilder;

    /**
     * Theme context
     *
     * @var \Magento\DesignEditor\Model\Theme\Context
     */
    protected $_themeContext;

    /**
     * Tab form HTML identifier
     *
     * @var string
     */
    protected $_formId = null;

    /**
     * Controls group which will be rendered on the tab form
     *
     * @var string
     */
    protected $_tab = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Form\Builder $formBuilder
     * @param \Magento\DesignEditor\Model\Theme\Context $themeContext
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Form\Builder $formBuilder,
        \Magento\DesignEditor\Model\Theme\Context $themeContext,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
        $this->_formBuilder = $formBuilder;
        $this->_themeContext = $themeContext;
    }

    /**
     * Create a form element with necessary controls
     *
     * @return \Magento\DesignEditor\Block\Adminhtml\Editor\Tools\QuickStyles\Header
     * @throws \Magento\Core\Exception
     */
    protected function _prepareForm()
    {
        if (!$this->_formId || !$this->_tab) {
            throw new \Magento\Core\Exception(
                __('We found an invalid block of class "%1". Please define the required properties.',
                    get_class($this))
            );
        }
        $form = $this->_formBuilder->create(array(
            'id'            => $this->_formId,
            'action'        => '#',
            'method'        => 'post',
            'tab'           => $this->_tab,
            'theme'         => $this->_themeContext->getStagingTheme(),
            'parent_theme'  => $this->_themeContext->getEditableTheme()->getParentTheme(),
        ));
        $form->setUseContainer(true);

        $this->setForm($form);

        parent::_prepareForm();
        return $this;
    }
}
