<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\Theme\Customization;

/**
 * Theme service file interface
 */
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
     * @param \Magento\View\Design\Theme\FileInterface $file
     * @return string
     */
    public function getFullPath(\Magento\View\Design\Theme\FileInterface $file);

    /**
     * Creates new custom file and binds to concrete service model
     *
     * @return \Magento\View\Design\Theme\FileInterface
     */
    public function create();

    /**
     * Saves related data to custom file
     *
     * @param \Magento\View\Design\Theme\FileInterface $file
     * @return $this
     */
    public function save(\Magento\View\Design\Theme\FileInterface $file);

    /**
     * Deletes related data to custom file
     *
     * @param \Magento\View\Design\Theme\FileInterface $file
     * @return $this
     */
    public function delete(\Magento\View\Design\Theme\FileInterface $file);

    /**
     * Prepare file content before it will be saved
     *
     * @param \Magento\View\Design\Theme\FileInterface $file
     * @return $this
     */
    public function prepareFile(\Magento\View\Design\Theme\FileInterface $file);
}
