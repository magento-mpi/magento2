<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Mage_Backend_Model_Config_Structure_ElementInterface
{
    /**
     * Set element data
     *
     * @param array $data
     */
    public function setData(array $data);

    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return bool
     */
    public function isDisplayed();
}

