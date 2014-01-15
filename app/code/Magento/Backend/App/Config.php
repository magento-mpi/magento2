<?php
/**
 * Default application path for backend area
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\App;

/**
 * Backend config accessor
 */
class Config implements ConfigInterface
{
    /**
     * @var \Magento\Core\Model\Config\SectionPool
     */
    protected $_sectionPool;

    /**
     * @param \Magento\Core\Model\Config\SectionPool $sectionPool
     */
    public function __construct(\Magento\Core\Model\Config\SectionPool $sectionPool)
    {
        $this->_sectionPool = $sectionPool;
    }

    /**
     * Retrieve config value by path and scope
     *
     * @param string $path
     * @return mixed
     */
    public function getValue($path)
    {
        return $this->_sectionPool->getSection('default', null)->getValue($path);
    }

    /**
     * Set config value in the corresponding config scope
     *
     * @param string $path
     * @param mixed $value
     */
    public function setValue($path, $value)
    {
        $this->_sectionPool->getSection('default', null)->setValue($path, $value);
    }

    /**
     * Reinitialize configuration
     */
    public function reinit()
    {
        $this->_sectionPool->clean();
    }

    /**
     * Retrieve config flag
     *
     * @param string $path
     * @return bool
     */
    public function getFlag($path)
    {
        return !!$this->_sectionPool->getSection('default', null)->getValue($path);
    }
}
