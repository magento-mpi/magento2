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
 * Tool block with buttons
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview;

class Buttons extends \Magento\Adminhtml\Block\Widget\Container
{
    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        array $data = array()
    ) {
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Adding two main buttons
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview\Buttons
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_addButton('preview', array(
                'id' => 'preview-buttons-preview',
                'label' => 'Preview',
                'class' => 'preview',
                'onclick' => 'preview()'
            ));

        if ($this->_cmsConfig->canCurrentUserPublishRevision()) {
            $this->_addButton('publish', array(
                'id' => 'preview-buttons-publish',
                'label' => 'Publish',
                'class' => 'publish',
                'onclick' => 'publish()'
            ));
        }
    }

    /**
     * Override parent method to produce only button's html in result
     *
     * @return string
     */
    protected function _toHtml()
    {
        parent::_toHtml();
        return $this->getButtonsHtml();
    }
}
