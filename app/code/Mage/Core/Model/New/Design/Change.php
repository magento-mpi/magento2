<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Core_Model_New_Design_Change
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

    public function getPageLayout()
    {
        return $this->pageLayout;
    }

    public function getLayoutUpdate()
    {
        return $this->layoutUpdate;
    }

    public function getCssString()
    {
        return $this->cssString;
    }
}
