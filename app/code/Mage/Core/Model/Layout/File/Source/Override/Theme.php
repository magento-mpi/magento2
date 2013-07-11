<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source of layout files that explicitly override files of ancestor themes
 *
 * @todo MAGETWO-11312 Implement Overriding of Theme Layout Files According to Proposal
 */
class Mage_Core_Model_Layout_File_Source_Override_Theme implements Mage_Core_Model_Layout_File_SourceInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getFiles(Mage_Core_Model_ThemeInterface $theme)
    {
        return array();
    }
}
