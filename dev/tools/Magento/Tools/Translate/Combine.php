<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/*

Usage:
 php -f combine.php -- --output <file> --locale <locale_NAME>

*/

define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(dirname(dirname(__DIR__))));

define('MESSAGE_TYPE_NOTICE', '0');
define('MESSAGE_TYPE_WARNING', '1');
define('MESSAGE_TYPE_ERROR', '2');

define('LOCALE_PATH', BASE_PATH . DS . 'app' . DS . 'locale' . DS . '%s' . DS);

include(BASE_PATH . DS . 'lib' . DS . 'Magento' . DS . 'File' . DS . 'Csv.php');
include(__DIR__ . DS . 'ModuleTranslations.php');

class Magento_Tools_Translate_Combine
{
    /**
     * File name patterns needed to be processed
     *
     * @var array
     */
    private $_namePatterns = array('#^(Magento_\w+)\.csv$#', '#^(translate).csv$#');

    /**
     * Pattern of the locale path
     *
     * @var string
     */
    private $_localePath = LOCALE_PATH;

    /**
     * Result output file name
     *
     * @var string
     */
    private $_outputFileName = null;

    /**
     * Locale name
     *
     * @var string
     */
    private $_localeName = null;

    /**
     * Messages array
     *
     * @var array
     */
    private $_messages = array();

    /**
     * Variable that indicates errors occurred
     *
     * @var bool
     */
    private $_error = false;

    /**
     * Combine init
     *
     * @param array $argv
     */
    public function __construct($argv)
    {
        $outputFileName = null;
        $localeName = null;
        $collectModules = false;

        foreach ($argv as $k=>$arg) {
            switch($arg) {
                case '--output':
                    $outputFileName = @$argv[$k+1];
                    break;

                case '--locale':
                    $localeName = @$argv[$k+1];
                    break;

                case '--collectmodules':
                    $collectModules = true;
                    break;
            }
        }

        if (!$outputFileName || !$localeName) {
            $this->_addMessage(MESSAGE_TYPE_ERROR,
                "Use this script as follows:\n\t
                .php --output <file> --locale <locale_NAME> [--collectmodules]");
            $this->_error = true;
            return;
        }

        if ($collectModules) {
            Magento_Tools_Translate_ModuleTranslations::collectTranslations($localeName);
        }

        if (file_exists($outputFileName) && !is_writable($outputFileName)) {
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("File '%s' exists and isn't writeable", $outputFileName));
            $this->_error = true;
            return;
        }

        if (!is_dir(sprintf($this->_localePath, $localeName)) && mkdir(sprintf($this->_localePath, $localeName))) {
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("Locale '%s' was not found", $localeName));
            $this->_error = true;
            return;
        }

        if (!is_readable(sprintf($this->_localePath, $localeName))) {
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("Locale '%s' is not readable", $localeName));
            $this->_error = true;
            return;
        }


        $this->_outputFileName = $outputFileName;
        $this->_localeName = $localeName;
    }


    /**
     * Browses the given directory and returns full file names
     * which matches internal name patterns
     *
     * @return array
     * @param string $path
     */
    private function _getFilesToProcess($path)
    {
        $result = array();
        $prefix = (substr($path, -1) == DS ? $path : $path . DS);

        $directory = dir($path);
        while (false !== ($file = $directory->read())) {
            foreach ($this->_namePatterns as $pattern){
                if (preg_match($pattern, $file, $matches)){
                    $alias = $matches[1];
                    $result[$alias] = $prefix . $file;
                }
            }
        }

        return $result;
    }

    /**
     * Combine process
     *
     */
    public function run()
    {
        if ($this->_error) {
            return false;
        }
        $resultData = array();

        $files = $this->_getFilesToProcess(sprintf($this->_localePath, $this->_localeName));
        $csv = new Magento_File_Csv();

        foreach ($files as $alias=>$file){
            $data = $csv->getData($file);
            for ($i = 0; $i < count($data); $i++){
                $data[$i] = array_merge(array($alias), $data[$i]);
            }
            $resultData = array_merge($resultData, $data);
        }
        $csv->saveData($this->_outputFileName, $resultData);

        $this->_addMessage(MESSAGE_TYPE_NOTICE, 'Translation combined successfully');
    }

    /**
     * Parses internal messages and returns them as a string
     *
     * @return string
     */
    public function renderMessages()
    {
        $result = array();

        foreach ($this->_messages as $message){
            $type = $message['type'];
            $text = $message['text'];

            switch($type){
                case MESSAGE_TYPE_ERROR:
                    $type = 'Error';
                    break;

                case MESSAGE_TYPE_WARNING:
                    $type = 'Warning';
                    break;

                case MESSAGE_TYPE_NOTICE:
                default:
                    $type = 'Notice';
                    break;
            }

            $result[] = sprintf('%s: %s', $type, $text);
        }

        return implode('\n', $result);
    }

    private function _addMessage($type, $message)
    {
        $this->_messages[] = array('type'=>$type, 'text'=>$message);
    }
}

$combine = new Magento_Tools_Translate_Combine($argv);
$combine->run();
echo $combine->renderMessages();
echo "\n\n";
