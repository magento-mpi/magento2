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
     * @var \Magento\Core\Model\Dir
     */
    protected $_dir;

    /**
     * @param string $baseDir
     * @param array $params
     * @param \Magento\Core\Model\Dir $dir
     * @param \Magento\Core\Model\Config\LoaderInterface $loader
     */
    public function __construct(
        $baseDir,
        array $params,
        \Magento\Core\Model\Dir $dir = null,
        \Magento\Core\Model\Config\LoaderInterface $loader = null
    ) {
        parent::__construct('<config/>');
        $this->_params = $params;
        $this->_dir = $dir ?: new \Magento\Core\Model\Dir(
            $baseDir,
            $this->getParam(\Magento\Core\Model\App::PARAM_APP_URIS, array()),
            $this->getParam(\Magento\Core\Model\App::PARAM_APP_DIRS, array())
        );
        \Magento\Autoload\IncludePath::addIncludePath(array(
            $this->_dir->getDir(\Magento\Core\Model\Dir::GENERATION)
        ));

        $this->_loader = $loader ?: new \Magento\Core\Model\Config\Loader\Primary(
            $this->_dir->getDir(\Magento\Core\Model\Dir::CONFIG)
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
     * Retrieve directories
     *
     * @return \Magento\Core\Model\Dir
     */
    public function getDirectories()
    {
        return $this->_dir;
    }

    /**
     * Reinitialize primary configuration
     */
    public function reinit()
    {
        $this->loadString('<config/>');
        $this->_loader->load($this);
    }

    /**
     * Retrieve class definition config
     *
     * @return string
     */
    public function getDefinitionPath()
    {
        $pathInfo = (array) $this->getNode('global/di/definitions');
        if (isset($pathInfo['path'])) {
            return $pathInfo['path'];
        } else if (isset($pathInfo['relativePath'])) {
            return $this->_dir->getDir(\Magento\Core\Model\Dir::ROOT) . DIRECTORY_SEPARATOR . $pathInfo['relativePath'];
        } else {
            return $this->_dir->getDir(\Magento\Core\Model\Dir::DI);
        }
    }

    /**
     * Retrieve definition format
     *
     * @return string
     */
    public function getDefinitionFormat()
    {
        return (string) $this->getNode('global/di/definitions/format');
    }

    /**
     * Configure object manager
     *
     * \Magento\Core\Model\ObjectManager $objectManager
     */
    public function configure(\Magento\Core\Model\ObjectManager $objectManager)
    {
        \Magento\Profiler::start('initial');

        $objectManager->configure(array(
            'Magento\Core\Model\Config\Loader\Local' => array(
                'parameters' => array(
                    'configDirectory' => $this->_dir->getDir(\Magento\Core\Model\Dir::CONFIG),
                )
            ),
            'Magento\Core\Model\Cache\Frontend\Factory' => array(
                'parameters' => array(
                    'decorators' => $this->_getCacheFrontendDecorators(),
                )
            ),
        ));

        \Magento\Profiler::stop('initial');
    }

    /**
     * Retrieve cache frontend decorators configuration
     *
     * @return array
     */
    protected function _getCacheFrontendDecorators()
    {
        $result = array();
        // mark all cache entries with a special tag to be able to clean only cache belonging to the application
        $result[] = array(
            'class' => 'Magento\Cache\Frontend\Decorator\TagScope',
            'parameters' => array('tag' => 'MAGE'),
        );
        if (\Magento\Profiler::isEnabled()) {
            $result[] = array(
                'class' => 'Magento\Cache\Frontend\Decorator\Profiler',
                'parameters' => array('backendPrefixes' => array('Zend_Cache_Backend_', 'Magento\Cache\Backend\\')),
            );
        }
        return $result;
    }
}
