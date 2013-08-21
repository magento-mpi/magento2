<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme service file interface
 */
interface Magento_Core_Model_Theme_Customization_FileInterface
{
    /**
     * Get type of file
     *
     * @return string
     */
    public function getType();

    /**
     * Gets absolute path to a custom file
     *
     * @param Magento_Core_Model_Theme_FileInterface $file
     * @return string
     */
    public function getFullPath(Magento_Core_Model_Theme_FileInterface $file);

    /**
     * Creates new custom file and binds to concrete service model
     *
     * @return Magento_Core_Model_Theme_FileInterface
     */
    public function create();

    /**
     * Saves related data to custom file
     *
     * @param Magento_Core_Model_Theme_FileInterface $file
     * @return $this
     */
    public function save(Magento_Core_Model_Theme_FileInterface $file);

    /**
     * Deletes related data to custom file
     *
     * @param Magento_Core_Model_Theme_FileInterface $file
     * @return $this
     */
    public function delete(Magento_Core_Model_Theme_FileInterface $file);

    /**
     * Prepare file content before it will be saved
     *
     * @param Magento_Core_Model_Theme_FileInterface $file
     * @return $this
     */
    public function prepareFile(Magento_Core_Model_Theme_FileInterface $file);
}
