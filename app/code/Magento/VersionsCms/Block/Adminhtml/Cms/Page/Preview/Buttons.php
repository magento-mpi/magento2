<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Page\Preview;

/**
 * Tool block with buttons
 */
class Buttons extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var \Magento\VersionsCms\Model\Config
     */
    protected $_cmsConfig;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\VersionsCms\Model\Config $cmsConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\VersionsCms\Model\Config $cmsConfig,
        array $data = array()
    ) {
        $this->_cmsConfig = $cmsConfig;
        parent::__construct($context, $data);
    }

    /**
     * Adding two main buttons
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->add(
            'preview',
            array(
                'id' => 'preview-buttons-preview',
                'label' => 'Preview',
                'class' => 'preview',
                'onclick' => 'preview()'
            ),
            0,
            0,
            null
        );

        if ($this->_cmsConfig->canCurrentUserPublishRevision()) {
            $this->buttonList->add(
                'publish',
                array(
                    'id' => 'preview-buttons-publish',
                    'label' => 'Publish',
                    'class' => 'publish',
                    'onclick' => 'publish()'
                ),
                0,
                0,
                null
            );
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
