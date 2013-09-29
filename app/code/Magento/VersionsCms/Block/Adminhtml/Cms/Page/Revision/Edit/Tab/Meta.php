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
 * Meta tab with cms page attributes and some modifications to CE version
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab;

class Meta
    extends \Magento\Adminhtml\Block\Cms\Page\Edit\Tab\Meta
{
    /**
     * Cms data
     *
     * @var \Magento\VersionsCms\Helper\Data
     */
    protected $_cmsData = null;

    /**
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\VersionsCms\Helper\Data $cmsData
     * @param \Magento\Data\Form\Factory $formFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\VersionsCms\Helper\Data $cmsData,
        \Magento\Data\Form\Factory $formFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_cmsData = $cmsData;
        parent::__construct($coreRegistry, $formFactory, $coreData, $context, $data);
    }

    /**
     * Adding onchange js call
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Revision\Edit\Tab\Meta
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        $this->_cmsData->addOnChangeToFormElements($this->getForm(), 'dataChanged();');

        return $this;
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
}
