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
 php -f split.php -- --input <file> --locale <locale_NAME>

*/


define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(dirname(dirname(__DIR__))));

define('MESSAGE_TYPE_NOTICE', '0');
define('MESSAGE_TYPE_WARNING', '1');
define('MESSAGE_TYPE_ERROR', '2');

define('LOCALE_PATH', BASE_PATH . DS . 'app' . DS . 'locale' . DS . '%s' . DS);

include(BASE_PATH . DS . 'lib' . DS . 'Magento' . DS . 'File' . DS . 'Csv.php');
include(__DIR__ . DS . 'ModuleTranslations.php');

class Magento_Tools_Translate_Split
{
    /**
     * Pattern of the locale path
     *
     * @var string
     */
    private $_localePath = LOCALE_PATH;

    /**
     * Result input file name
     *
     * @var string
     */
    private $_inputFileName = null;

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
     * Copy translation files to the module's directory
     *
     * @var bool
     */
    private $_distribute = false;

    /**
     * Clean locale directory
     *
     * @var bool
     */
    private $_clean = false;

    /**
     * Variable that indicates errors occurred
     *
     * @var bool
     */
    private $_error = false;

    /**
     * Split init
     *
     * @param array $argv
     */
    public function __construct($argv)
    {
        $inputFileName = null;
        $localeName = null;

        foreach ($argv as $k=>$arg) {
            switch($arg) {
                case '--input':
                    $inputFileName = @$argv[$k+1];
                    break;

                case '--locale':
                    $localeName = @$argv[$k+1];
                    break;

                case '--distribute':
                    $this->_distribute = true;
                    break;

                case '--clean':
                    $this->_clean = true;
                    break;
            }
        }

        if (!$inputFileName || !$localeName) {
            $this->_addMessage(MESSAGE_TYPE_ERROR,
                "Use this script as follows:\n"
                . "\tsplit.php --input <file> --locale <locale_NAME> [--distribute [--clean]]");
            $this->_error = true;
            return;
        }

        if (!file_exists($inputFileName)){
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("File '%s' doesn't exists", $inputFileName));
            $this->_error = true;
            return;
        }

        if (!is_readable($inputFileName)){
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("File '%s' isn't readable", $inputFileName));
            $this->_error = true;
            return;
        }

        if (!is_dir(sprintf($this->_localePath, $localeName))){
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("Locale '%s' was not found", $localeName));
            $this->_error = true;
            return;
        }

        if (!is_writable(sprintf($this->_localePath, $localeName))){
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("Locale '%s' is not writeable", $localeName));
            $this->_error = true;
            return;
        }


        $this->_inputFileName = $inputFileName;
        $this->_localeName = $localeName;
    }

    /**
     * Split process
     *
     * @return bool
     */
    public function run()
    {
        if ($this->_error) {
            return false;
        }

        $csv = new \Magento\File\Csv();
        $inputData = $csv->getData($this->_inputFileName);
        $output = array();

        foreach ($inputData as $row){
            $output[$row[0]][] = array_slice($row, 1);
        }

        foreach ($output as $file=>$data){
            $outputFileName = sprintf($this->_localePath, $this->_localeName) . "{$file}.csv";
            $csv->saveData($outputFileName, $data);
        }

        $this->_addMessage(MESSAGE_TYPE_NOTICE, 'Translation splitted successfully');

        if ($this->_distribute) {
            Magento_Tools_Translate_ModuleTranslations::distributeTranslations($this->_localeName);
            if ($this->_clean) {
                Magento_Tools_Translate_ModuleTranslations::cleanTranslations($this->_localeName);
            }
        }
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

$split = new Magento_Tools_Translate_Split($argv);
$split->run();
echo $split->renderMessages();
echo "\n\n";
