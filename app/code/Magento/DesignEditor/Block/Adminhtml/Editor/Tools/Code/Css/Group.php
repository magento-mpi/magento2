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
 * Block that renders group of files
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Tools\Code\Css;

class Group extends \Magento\Backend\Block\Widget\Form
{
    /**
     * Get url to download CSS file
     *
     * @param string $fileId
     * @param int $themeId
     * @return string
     */
    public function getDownloadUrl($fileId, $themeId)
    {
        return $this->getUrl('*/system_design_theme/downloadCss', array(
            'theme_id' => $themeId,
            'file'     => $this->_helperFactory->get('Magento\DesignEditor\Helper\Data')->urlEncode($fileId)
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
