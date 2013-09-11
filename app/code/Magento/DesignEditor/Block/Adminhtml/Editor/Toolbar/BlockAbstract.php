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
 * Abstract toolbar block
 */
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar;

abstract class BlockAbstract extends \Magento\Backend\Block\Template
{
    /**
     * Current VDE mode
     *
     * @var int
     */
    protected $_mode;

    /**
     * Get current VDE mode
     *
     * @return int
     */
    public function getMode()
    {
        return $this->_mode;
    }

    /**
     * Get current VDE mode
     *
     * @param int $mode
     * @return \Magento\DesignEditor\Block\Adminhtml\Editor\Toolbar\BlockAbstract
     */
    public function setMode($mode)
    {
        $this->_mode = $mode;

        return $this;
    }

    /**
     * Check if visual editor is in navigation mode
     *
     * @return bool
     */
    public function isNavigationMode()
    {
        return $this->getMode() == \Magento\DesignEditor\Model\State::MODE_NAVIGATION;
    }
}
