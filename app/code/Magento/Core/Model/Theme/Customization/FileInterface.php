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
namespace Magento\Core\Model\Theme\Customization;

interface FileInterface
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
     * @param \Magento\Core\Model\Theme\FileInterface $file
     * @return string
     */
    public function getFullPath(\Magento\Core\Model\Theme\FileInterface $file);

    /**
     * Creates new custom file and binds to concrete service model
     *
     * @return \Magento\Core\Model\Theme\FileInterface
     */
    public function create();

    /**
     * Saves related data to custom file
     *
     * @param \Magento\Core\Model\Theme\FileInterface $file
     * @return $this
     */
    public function save(\Magento\Core\Model\Theme\FileInterface $file);

    /**
     * Deletes related data to custom file
     *
     * @param \Magento\Core\Model\Theme\FileInterface $file
     * @return $this
     */
    public function delete(\Magento\Core\Model\Theme\FileInterface $file);

    /**
     * Prepare file content before it will be saved
     *
     * @param \Magento\Core\Model\Theme\FileInterface $file
     * @return $this
     */
    public function prepareFile(\Magento\Core\Model\Theme\FileInterface $file);
}
