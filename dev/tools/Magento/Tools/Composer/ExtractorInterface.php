<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tools\Composer;

/**
 * Interface ExtractorInterface
 */
interface ExtractorInterface
{

    /**
     * Extract and Create Components
     *
     * @param array $collection
     * @param int &$count
     * @return array
     */
    public function extract(array $collection = array(), &$count = 0);

    /**
     * Creates Package based on given configuration
     *
     * @param array $definition
     * @return \Magento\Tools\Composer\Model\Package
     */
    public function create(array $definition);

    /**
     * Sets configuration information to a Package
     *
     * @param \Magento\Tools\Composer\Model\Package &$component
     * @param array $definition
     * @return \Magento\Tools\Composer\Model\Package
     */
    public function setValues(\Magento\Tools\Composer\Model\Package &$component, array $definition);

    /**
     * Retrieve Type of Composer Package
     *
     * @return string|null
     */
    public function getType();

    /**
     * Retrieve Location of Component
     *
     * @return string|null
     */
    public function getPath();


}