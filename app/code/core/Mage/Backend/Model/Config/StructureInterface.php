<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config structure interface
 */
interface Mage_Backend_Model_Config_StructureInterface extends IteratorAggregate
{
    /**
     * Get section configuration
     *
     * @param string $sectionCode
     * @return Mage_Backend_Model_Config_Structure_ElementInterface
     */
    public function getSection($sectionCode);
}
