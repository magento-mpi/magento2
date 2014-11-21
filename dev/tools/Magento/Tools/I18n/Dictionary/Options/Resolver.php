<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n\Dictionary\Options;

/**
 * Dictionary generator options resolver
 */
class Resolver implements ResolverInterface
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var bool
     */
    protected $withContext;

    /**
     * Resolver construct
     *
     * @param string $directory
     * @param bool $withContext
     */
    public function __construct($directory, $withContext)
    {
        $this->directory = $directory;
        $this->withContext = $withContext;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        if (null === $this->options) {
            if ($this->withContext) {
                $this->directory = rtrim($this->directory, '\\/');
                $this->options = array(
                    array(
                        'type' => 'php',
                        'paths' => array($this->directory . '/app/code/', $this->directory . '/app/design/'),
                        'fileMask' => '/\.(php|phtml)$/'
                    ),
                    array(
                        'type' => 'js',
                        'paths' => array(
                            $this->directory . '/app/code/',
                            $this->directory . '/app/design/',
                            $this->directory . '/lib/web/mage/',
                            $this->directory . '/lib/web/varien/'
                        ),
                        'fileMask' => '/\.(js|phtml)$/'
                    ),
                    array(
                        'type' => 'xml',
                        'paths' => array($this->directory . '/app/code/', $this->directory . '/app/design/'),
                        'fileMask' => '/\.xml$/'
                    )
                );
            } else {
                $this->options = array(
                    array('type' => 'php', 'paths' => array($this->directory), 'fileMask' => '/\.(php|phtml)$/'),
                    array('type' => 'js', 'paths' => array($this->directory), 'fileMask' => '/\.(js|phtml)$/'),
                    array('type' => 'xml', 'paths' => array($this->directory), 'fileMask' => '/\.xml$/')
                );
            }
            foreach ($this->options as $option) {
                $this->isValidPaths($option['paths']);
            }
        }
        return $this->options;
    }

    /**
     * @param array $directories
     * @return void
     * @throws \InvalidArgumentException
     */
    protected function isValidPaths($directories)
    {
        foreach ($directories as $path) {
            if (!is_dir($path)) {
                if ($this->withContext) {
                    throw new \InvalidArgumentException('Specified path is not a Magento root directory');
                } else {
                    throw new \InvalidArgumentException('Specified path doesn\'t exist');
                }
            }
        }
    }
}
