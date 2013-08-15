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
class Mage_Page_Block_Links extends Mage_Core_Block_Template
{
    /** @var string */
    protected $_template = 'links.phtml';

    /**
     * @return Mage_Page_Block_Link[]
     */
    public function getLinks()
    {
        return $this->_layout->getChildBlocks($this->getNameInLayout());
    }

    /**
     * Render Block
     *
     * @param Mage_Core_Block_Abstract $link
     * @return string
     */
    public function renderLink(Mage_Core_Block_Abstract $link)
    {
        return $this->_layout->renderElement($link->getNameInLayout());
    }
}
