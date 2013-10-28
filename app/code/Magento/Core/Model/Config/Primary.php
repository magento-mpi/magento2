<?php
/**
 * Primary application config (app/etc/*.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class Primary extends \Magento\Core\Model\Config\Base
{
    /**
     * @var \Magento\Core\Model\Config\Loader\Primary
     */
    protected $_loader;

    /**
     * Application parameter list
     *
     * @var array
     */
    protected $_params;

    /**
     * Directory list
     *
     * @var \Magento\App\Dir
     */
    protected $_dir;

    /**
     * @param string $baseDir
     * @param array $params
     * @param \Magento\App\Dir $dir
     * @param \Magento\Core\Model\Config\LoaderInterface $loader
     */
    public function __construct(
        $baseDir,
        array $params,
        \Magento\App\Dir $dir = null,
        \Magento\Core\Model\Config\LoaderInterface $loader = null
    ) {
        parent::__construct('<config/>');
        $this->_params = $params;

        $this->_loader = $loader ?: new \Magento\Core\Model\Config\Loader\Primary(
            $this->_dir->getDir(\Magento\App\Dir::CONFIG)
        );
        $this->_loader->load($this);
    }

    /**
     * Get init param
     *
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getParam($name, $defaultValue = null)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : $defaultValue;
    }

    /**
     * Get application init params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Reinitialize primary configuration
     */
    public function reinit()
    {
        $this->loadString('<config/>');
        $this->_loader->load($this);
    }
}
