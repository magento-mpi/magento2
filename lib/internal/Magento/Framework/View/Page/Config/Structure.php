<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Framework\View\Page\Config;

/**
 * Page config structure model
 */
class Structure
{
    /**
     * Information assets elements on page
     *
     * @var array
     */
    protected $assets = [];

    /**
     * List asset which will be removed
     *
     * @var array
     */
    protected $removeAssets = [];

    /**
     * @param string $name
     * @param array $attributes
     * @return $this
     */
    public function addAssets($name, $attributes)
    {
        $this->assets[$name] = $attributes;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function removeAssets($name)
    {
        $this->removeAssets[$name] = $name;
        return $this;
    }

    /**
     * @return $this
     */
    public function processRemoveAssets()
    {
        $this->assets = array_diff_key($this->assets, $this->removeAssets);
        $this->removeAssets = [];
        return $this;
    }

    /**
     * @return array
     */
    public function getAssets()
    {
        return $this->assets;
    }
}
