<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Links list block
 */
class Magento_Page_Block_Links extends Magento_Core_Block_Template
{
    /** @var string */
    protected $_template = 'links.phtml';

    /**
     * @return Magento_Page_Block_Link[]
     */
    public function getLinks()
    {
        return $this->_layout->getChildBlocks($this->getNameInLayout());
    }

    /**
     * Render Block
     *
     * @param Magento_Core_Block_Abstract $link
     * @return string
     */
    public function renderLink(Magento_Core_Block_Abstract $link)
    {
        return $this->_layout->renderElement($link->getNameInLayout());
    }
}
