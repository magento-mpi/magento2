<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Code\Css;

/**
 * Block that renders group of files
 */
class Group extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\DesignEditor\Helper\Data
     */
    protected $_designEditorHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\DesignEditor\Helper\Data $designEditorHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\DesignEditor\Helper\Data $designEditorHelper,
        array $data = array()
    ) {
        $this->_designEditorHelper = $designEditorHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get url to download CSS file
     *
     * @param string $fileId
     * @param int $themeId
     * @return string
     */
    public function getDownloadUrl($fileId, $themeId)
    {
        return $this->getUrl('adminhtml/system_design_theme/downloadCss', array(
            'theme_id' => $themeId,
            'file'     => $this->_designEditorHelper->urlEncode($fileId)
        ));
    }

    /**
     * Check if files group needs "add" button
     *
     * @return bool
     */
    public function hasAddButton()
    {
        return false;
    }

    /**
     * Check if files group needs download buttons next to each file
     *
     * @return bool
     */
    public function hasDownloadButton()
    {
        return true;
    }
}
