<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme service file interface
 */
interface Mage_Core_Model_Theme_Customization_FileInterface
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
     * @param Mage_Core_Model_Theme_FileInterface $file
     * @return string
     */
    public function getFullPath(Mage_Core_Model_Theme_FileInterface $file);

    /**
     * Creates new custom file and binds to concrete service model
     *
     * @return Mage_Core_Model_Theme_FileInterface
     */
    public function create();

    /**
     * Saves related data to custom file
     *
     * @param Mage_Core_Model_Theme_FileInterface $file
     * @return $this
     */
    public function save(Mage_Core_Model_Theme_FileInterface $file);

    /**
     * Deletes related data to custom file
     *
     * @param Mage_Core_Model_Theme_FileInterface $file
     * @return $this
     */
    public function delete(Mage_Core_Model_Theme_FileInterface $file);

    /**
     * Prepare file content before it will be saved
     *
     * @param Mage_Core_Model_Theme_FileInterface $file
     * @return $this
     */
    public function prepareFile(Mage_Core_Model_Theme_FileInterface $file);
}
