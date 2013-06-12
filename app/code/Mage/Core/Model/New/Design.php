<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_New_Design
{
    protected $theme;
    protected $pageLayout;
    protected $layoutUpdate;
    protected $cssString;

    public function __construct() {}
    
    public function getTheme()
    {
        return $this->theme;
    }

    public function applyChange(Mage_Core_Model_New_Design_Change $designChange)
    {
        $this->theme = $designChange->getTheme();
        $this->pageLayout = $designChange->getPageLayout();
        $this->layoutUpdate = $designChange->getLayoutUpdate();
        $this->cssString = $designChange->getCssString();
    }
}
