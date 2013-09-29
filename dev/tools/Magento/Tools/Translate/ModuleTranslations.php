<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Translate;

require_once __DIR__ . '/config.inc.php';
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(dirname(dirname(__DIR__))));
}

define('MT_USAGE', <<<MTUSAGE
USAGE:
---------------------------------------------------------------------------------
Collect translation files from modules to app/locale/locale_NAME directory:
$> php -f module_translates.php -- --collect locale_NAME

Distribute translation files from app/locale/locale_NAME to modules directory:
$> php -f module_translates.php -- --distribute locale_NAME [--clean]
---------------------------------------------------------------------------------

OPTIONAL PARAMETRS:
-------------------------------------------------------------------------------
--clean      Clean app/locale/locale_NAME
-------------------------------------------------------------------------------

MTUSAGE
);

global $argv;
global $CONFIG;

class ModuleTranslations
{
    const ACTION_CLEAN = 1;
    const ACTION_COLLECT = 2;
    const ACTION_DISTRIBUTE = 4;
    /**
     * Requested action
     *
     * @var string
     */
    protected $_action = '';

    /**
     * Locale name (ex. en_US)
     *
     * @var string
     */
    protected $_locale = '';

    /**
     * Config data
     *
     * @var array
     */
    protected static $_config = array();

    /**
     * Initialize data
     *
     * @param array $argv
     */
    function __construct($argv = array())
    {
        if (!(!empty($argv) && ($argv[0] == __FILE__ || $argv[0] == basename(__FILE__)))) {
            return;
        }
        foreach ($argv as $k=>$arg) {
            switch($arg) {
                case '--collect':
                    if (empty($this->_action)) {
                        $this->_action = self::ACTION_COLLECT;
                        $this->_locale = @$argv[$k+1];
                    }
                    break;

                case '--distribute':
                    if (empty($this->_action)) {
                        $this->_action = self::ACTION_DISTRIBUTE;
                        $this->_locale = @$argv[$k+1];
                    }
                    break;
                case '--clean':
                    $this->_action |= self::ACTION_CLEAN;
                    break;
            }
        }
        if (empty($this->_action)) {
            echo MT_USAGE;
        }
    }

    /**
     * Collect translation files from modules directories to the app/locale/ directory
     *
     * @param string $locale Locale name (ex. en_US)
     * @return bool
     * @throws \Exception
     */
    public static function collectTranslations($locale='')
    {
        $writeError = false;
        $_config = self::getConfig();
        $localeDir = BASE_PATH . DS . $_config['paths']['locale'] . $locale;
        if (!is_dir($localeDir)) {
            if (!mkdir($localeDir, 0777, true)) {
                $writeError = true;
            }
        } else {
            if(!is_writable($localeDir)) {
                $writeError = true;
            }
        }
        if ($writeError) {
            throw new \Exception("Directory $localeDir is not writable \n\n");
        }

        $files = glob(BASE_PATH . DS . 'app' . DS . 'code' . DS . '*' . DS . '*' . DS .  'locale'
            . DS . $locale . DS . '*.' . EXTENSION);
        $newFileMask = $localeDir . DS . '%s';
        foreach ($files as $file) {
            copy($file, sprintf($newFileMask, basename($file)));
        }
        return true;
    }

    /**
     * Distribute (copy) translation files from the app/locale/ directory to modules directories
     *
     * @param string $locale Locale name (ex. en_US)
     * @return bool
     * @throws \Exception
     */
    public static function distributeTranslations($locale)
    {
        $writeError = false;
        $_config = self::getConfig();
        $pathLocales = $_config['paths']['locale'];
        $localeDir = BASE_PATH . DS . $pathLocales . $locale;
        if (!is_dir($localeDir)) {
            throw new \Exception("Directory $localeDir is not writable \n\n");
        }

        $files = glob($localeDir . DS . '*.' . EXTENSION);
        $newFileMask = BASE_PATH . DS . 'app' . DS . 'code' . DS . '%s' . DS . '%s' . DS .  'locale'
            . DS . $locale . DS . '%s' . '.' . EXTENSION;
        foreach ($files as $file) {
            $baseFileName = basename($file, '.' . EXTENSION);
            $parts = explode('_', $baseFileName);
            $namespace = $parts[0];
            $module = $parts[1];

            $newFileName = sprintf($newFileMask, $namespace, $module, $baseFileName);
            $newFilePath = dirname($newFileName);
            if (!is_dir($newFilePath)) {
                if (!mkdir($newFilePath, 0777, true)) {
                    $writeError = true;
                }
            }
            if ($writeError || !copy($file, $newFileName)) {
                throw new \Exception("Directory $newFilePath is not writable \n\n");
            }
        }
        return true;
    }

    /**
     * Remove translation files from app/locale/$locale directory
     *
     * @param string $locale Locale name (ex. en_US)
     * @return bool
     * @throws \Exception
     */
    public static function cleanTranslations($locale)
    {
        $_config = self::getConfig();
        $localeDir = BASE_PATH . DS . $_config['paths']['locale'] . $locale;
        if (!is_dir($localeDir)) {
            throw new \Exception("Directory $localeDir is not writable \n\n");
        }
        $files = glob($localeDir . DS . '*.' . EXTENSION);
        foreach ($files as $file) {
            if (is_writable($file)) {
                unlink($file);
            }
        }

        $files = glob($localeDir . DS . '*');
        if (empty($files)) {
            rmdir($localeDir);
            $files = glob(dirname($localeDir) . DS . '*' . DS);
            if (empty($files)) {
                rmdir(dirname($localeDir));
            }
        }
        return true;
    }

    /**
     * Set config
     *
     * @static
     * @param array $config
     */
    public static function setConfig($config = array())
    {
        self::$_config = $config;
    }

    /**
     * Retrieve config
     *
     * @static
     * @return array
     */
    public static function getConfig()
    {
        return self::$_config;
    }

    /**
     * Run actions passed from command line
     */
    public function run()
    {
        if ($this->_action & self::ACTION_COLLECT) {
            $this->collectTranslations($this->_locale);
        }
        if ($this->_action & self::ACTION_DISTRIBUTE) {
            $this->distributeTranslations($this->_locale);
        }
        if ($this->_action & self::ACTION_CLEAN) {
            $this->cleanTranslations($this->_locale);
        }
    }
}

\Magento\Tools\Translate\ModuleTranslations::setConfig($CONFIG);
$moduleTranslation = new \Magento\Tools\Translate\ModuleTranslations($argv);
$moduleTranslation->run();
echo "\n\n";
