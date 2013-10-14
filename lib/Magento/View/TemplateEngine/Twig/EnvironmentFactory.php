<?php
/**
 * Factory is used to hide the details of how a Twig Environment is built.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace \Magento\View\TemplateEngine\Twig;

class Magento_View_TemplateEngine_Twig_EnvironmentFactory
{
    /**
     * @var Magento_View_TemplateEngine_Twig_Extension
     */
    protected $_extension;

    /**
     * @var Magento_Filesystem
     */
    protected $_filesystem;
    
    /**
     * @var Twig_Environment
     */
    private $_environment;

    /**
     * @var Magento_View_Dir
     */
    private $_dir;
    
    /**
     * @var Magento_View_Logger
     */
    private $_logger;

    /**
     * @var Twig_LoaderInterface
     */
    private $_loader;

    /**
     * Create new instance of factory
     *
     * @param Magento_Filesystem $filesystem
     * @param Magento_View_TemplateEngine_Twig_Extension $extension
     * @param Magento_View_Dir $dir
     * @param Magento_View_Logger $logger
     * @param Twig_LoaderInterface $loader
     */
    public function __construct(
        Magento_Filesystem $filesystem,
        Magento_View_TemplateEngine_Twig_Extension $extension,
        Magento_View_Dir $dir,
        Magento_View_Logger $logger,
        Twig_LoaderInterface $loader
    ) {
        $this->_filesystem = $filesystem;
        $this->_extension = $extension;
        $this->_dir = $dir;
        $this->_logger = $logger;
        $this->_loader = $loader;
        $this->_environment = null;
    }

    /**
     * Initialize (if necessary) and return the Twig environment.
     *
     * @return Twig_Environment
     */
    public function create()
    {
        if ($this->_environment === null) {
            $this->_environment = new Twig_Environment($this->_loader);
            try {
                $precompiledTmpltDir = $this->_dir->getDir(Magento_View_Dir::VAR_DIR) . '/twig_templates';
                $this->_filesystem->createDirectory($precompiledTmpltDir);
                $this->_environment->setCache($precompiledTmpltDir);
            } catch (Magento_Filesystem_Exception $e) {
                // Twig will just run slowly but not worth stopping Magento for it
                $this->_logger->logException($e);
            } catch (InvalidArgumentException $e) {
                // Can happen if path isn't found, shouldn't stop Magento
                $this->_logger->logException($e);
            }
            $this->_environment->enableStrictVariables();
            $this->_environment->addExtension(new Twig_Extension_Escaper('html'));
            $this->_environment->addExtension(new Twig_Extension_Optimizer(1));
            $this->_environment->addExtension($this->_extension);
            $this->_environmentInitialized = true;
        }
        return $this->_environment;
    }
}
