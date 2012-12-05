<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Visual Design Editor Preview Default Mode
 */
class Mage_DesignEditor_Model_Theme_Preview_Default extends  Mage_DesignEditor_Model_Theme_Preview_Abstract
{
    /**
     * Visual Design Editor Session
     *
     * @var Mage_DesignEditor_Model_Session
     */
    protected $_designSession;

    /**
     * Initialize preview mode
     *
     * @param Mage_DesignEditor_Model_Session $designSession
     */
    public function __construct(Mage_DesignEditor_Model_Session $designSession)
    {
        $this->_designSession = $designSession;
    }

    /**
     * Return preview url
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->_designSession->setThemeId($this->getTheme()->getId())->getPreviewUrl();
    }
}
