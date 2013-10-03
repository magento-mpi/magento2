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
 * VDE buttons block
 *
 * @method \Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar\Buttons setVirtualThemeId(int $id)
 * @method int getVirtualThemeId()
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar;

class Buttons
    extends \Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar\AbstractBlock
{
    /**
     * Current theme used for preview
     *
     * @var int
     */
    protected $_themeId;

    /**
     * Get current theme id
     *
     * @return int
     */
    public function getThemeId()
    {
        return $this->_themeId;
    }

    /**
     * Get current theme id
     *
     * @param int $themeId
     * @return \Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar\Buttons
     */
    public function setThemeId($themeId)
    {
        $this->_themeId = $themeId;

        return $this;
    }

    /**
     * Get admin panel home page URL
     *
     * @return string
     */
    public function getHomeLink()
    {
        return $this->helper('Magento\Backend\Helper\Data')->getHomePageUrl();
    }
}
