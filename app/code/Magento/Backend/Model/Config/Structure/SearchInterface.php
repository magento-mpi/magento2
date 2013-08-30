<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Backend_Model_Config_Structure_SearchInterface
{
    /**
     * Find element by path
     *
     * @param string $path
     * @return Magento_Backend_Model_Config_Structure_ElementInterface|null
     */
    public function getElement($path);
}
