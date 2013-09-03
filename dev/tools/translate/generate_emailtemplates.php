<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

define('USAGE', <<<USAGE
 Create translation file(s) from e-mail templates of locale
  php -f generate_emailtemplates.php -- --locale <locale_NAME> --output <file|directory>

 Apply translation to e-mail templates
  php -f generate_emailtemplates.php -- --locale <locale_NAME> --translate <CSV file|directory> --output <directory>

 Merge two different locales
  php -f generate_emailtemplates.php -- --merge-locales en_US-translate.csv de_DE-translate.csv de_DE.csv

 Split one translation file with sections to the separate files
  php -f generate_emailtemplates.php -- --split en_US.csv

USAGE
);


define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(dirname(dirname(__DIR__))));

define('MESSAGE_TYPE_NOTICE', '0');
define('MESSAGE_TYPE_WARNING', '1');
define('MESSAGE_TYPE_ERROR', '2');

define('ACTION_PROCESS_TEMPLATE', 1);
define('ACTION_MERGE_LOCALES', 2);
define('ACTION_SPLIT', 3);

define('LOCALE_PATH', BASE_PATH . DS . 'app' . DS . 'locale' . DS . '%s' . DS . 'template' . DS);

include(BASE_PATH . DS . 'lib' . DS . 'Magento' . DS . 'File' . DS . 'Csv.php');

class Generate
{
    /**
     * File name patterns needed to be processed
     *
     * @var array
     */
    protected $_namePatterns = array('#^(Magento_\w+)\.csv$#', '#^(translate).csv$#');

    /**
     * Pattern of the locale path
     *
     * @var string
     */
    protected $_localePath = LOCALE_PATH;

    /**
     * Result output file|dir name
     *
     * @var string
     */
    protected $_outputDirName = null;

    /**
     * Translation file|dir name
     *
     * @var string
     */
    protected $_translateName = null;

    /**
     * Locale name
     *
     * @var string
     */
    protected $_localeName = null;

    /**
     * Messages array
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Arguments array
     *
     * @var array
     */
    protected $_arguments = array();

    /**
     * Action
     *
     * @var int
     */
    protected $_action = null;

    /**
     * Variable that indicates errors occurred
     *
     * @var bool
     */
    protected $_error = false;

    protected function _is_writable($filePath)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $f = @fopen($filePath, 'a');
            if ($f !== false) {
                fclose($f);
                return true;
            } else {
                return false;
            }
        } else {
            return is_writable($filePath);
        }
    }

    /**
     * Combine init
     *
     * @param array $argv
     */
    public function __construct($argv)
    {
        $outputName = null;
        $localeName = null;
        $translateName = null;
        $action = null;

        foreach ($argv as $k=>$arg) {
            switch($arg) {
                case '--split':
                    $inputName = @$argv[$k+1];
                    $this->_action = ACTION_SPLIT;
                    break;

                case '--merge-locales':
                    $inputName1 = @$argv[$k+1];
                    $inputName2 = @$argv[$k+2];
                    $outputName = @$argv[$k+3];
                    $this->_action = ACTION_MERGE_LOCALES;
                    break;

                case '--output':
                    $outputName = @$argv[$k+1];
                    break;

                case '--locale':
                    $localeName = @$argv[$k+1];
                    break;

                case '--translate':
                    $translateName = @$argv[$k+1];
                    break;
            }
        }

        if (ACTION_SPLIT == $this->_action) {
            if (!empty($inputName)) {
                $this->_arguments = array(
                    'inputName' => $inputName,
                );
                return;
            }
        }

        if (ACTION_MERGE_LOCALES == $this->_action) {
            if (!empty($inputName1) && !empty($inputName2) && !empty($outputName)) {
                $this->_arguments = array(
                    'inputName1' => $inputName1,
                    'inputName2' => $inputName2,
                    'outputName' => $outputName,
                );
                return;
            }
        }

        if (!$outputName || !$localeName) {
            $this->_addMessage(MESSAGE_TYPE_ERROR, "Use this script as follows:\n" . USAGE);
            $this->_error = true;
            return;
        }

        if (file_exists($outputName) && !is_writable($outputName)){
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("File '%s' exists and isn't writeable", $outputName));
            $this->_error = true;
            return;
        }

        if (!is_dir(sprintf($this->_localePath, $localeName))){
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("Locale '%s' was not found", $localeName));
            $this->_error = true;
            return;
        }

        if (!is_readable(sprintf($this->_localePath, $localeName))){
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("Locale '%s' is not readable", $localeName));
            $this->_error = true;
            return;
        }

        if (!is_dir($outputName) && file_exists($outputName) && !$this->_is_writable($outputName)) {
            $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("Output file '%s' is not writable", $outputName));
            $this->_error = true;
            return;
        }

        if ((substr($outputName, -1) == DS || substr($outputName, -1) == '/') && !file_exists($outputName)) {
            mkdir($outputName, 0777, true);
        }

        if ($translateName) {
            if (is_dir($translateName) && file_exists($translateName)) {
                $translateName = rtrim($translateName, '/\\') . DS;
            } elseif (!file_exists($translateName)) {
                $this->_addMessage(MESSAGE_TYPE_ERROR, sprintf("Translation '%s' was not found", $localeName));
                $this->_error = true;
                return;
            }
            $this->_translateName = $translateName;
        }

        $this->_outputDirName = $outputName;
        $this->_localeName = $localeName;
        $this->_action = ACTION_PROCESS_TEMPLATE;
    }


    /**
     * Does not support flag GLOB_BRACE
     *
     * @param string $pattern
     * @param int $flags
     * @return array
     */
    protected function glob_recursive($pattern, $flags = 0)
    {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . DS . '*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir)
        {
            $files = array_merge($files, $this->glob_recursive($dir . DS . basename($pattern), $flags));
        }
        return $files;
    }

    /**
     * Browses the given directory and returns full file names
     * which matches internal name patterns
     *
     * @return array
     * @param string $path
     */
    protected function _getFilesToProcess($path, $pattern = "*.html")
    {
        $result = array();
        $prefix = (substr($path, -1) == DS ? $path : $path . DS);

        $files = $this->glob_recursive($prefix . $pattern);
        foreach ($files as $filename) {
            $result[pathinfo($filename, PATHINFO_FILENAME)]=$filename;
        }
        return $result;
    }

    /**
     * Retrieve strings from templates and write to CSV
     */
    protected function _fromTemplateToCsv()
    {
        $resultData = array();
        $outputSeparate = is_dir($this->_outputDirName);

        $translatedDirName = dirname($this->_outputDirName) . DS . $this->_localeName.'_templates' . DS;

        if (!file_exists($translatedDirName)) {
            mkdir($translatedDirName, 0777, true);
        }

        $localePath = sprintf($this->_localePath, $this->_localeName);

        $files = $this->_getFilesToProcess($localePath);
        $csv = null;
        if (!$outputSeparate) {
            $csv = new Magento_File_Csv();
        }

        foreach ($files as $alias=>$file){
            if ($outputSeparate) {
                $csv = new Magento_File_Csv();
                $resultData = array();
            }

//            $data = $csv->getData($file);

            $data = file_get_contents($file);
            //remove comments
            $data = preg_replace(
                array(
                    '/\<\!\-\-[\s\S]*?\-\-\>/i',
                    '/\{\*[\s\S]*?\*\}/i'
                ),
                '', $data
            );
            $strings = preg_match_all('/\s*(<[^>]+>)*\s*([^<>\n]+)*/i', $data, $matches, PREG_OFFSET_CAPTURE);
            if ($strings) {
                $matchedGaps = array_reverse($matches[2]);
                $_resultData = array();
                foreach($matchedGaps as $found) {
                    if(!is_array($found)) {
                        continue;
                    }
                    $offset = $found[1];
                    $key = trim(trim($found[0], "-,.: |\n\r"));
                    $key = preg_replace(
                        array(
                            '/^\&[a-z0-9]{1,6}\;/',
                            '/\&[a-z0-9]{1,6}\;$/'
                        ), '', $key
                    );
                    $key = trim(trim($key, ";,.: |\n\r"));
                    $varName = preg_match('/^\{\{[\s\S]*?\}\}$/i', $key);
                    if(!empty($key) && !$varName) {
                        //Use for debug:
                        //$resultData[] = array($alias, $key, '<span style="background:red;">'.$key.'</span>');
                        if ($outputSeparate) {
                            $_resultData[] = array($key, $key);
                        } else {
                            $_resultData[] = array($alias, $key, $key);
                        }
                        $data = substr($data, 0, $offset)
                            . str_replace($key, '__(\''.$key.'\')__', substr($data, $offset, strlen($found[0])))
                            . substr($data, $offset + strlen($found[0]));
                    }
                }
                $resultData = array_merge($resultData, array_reverse($_resultData));
            }

            if ($outputSeparate) {
                $csv->saveData($this->_outputDirName . DS . $alias . '.csv', $resultData);
                unset($csv);
            }
            $translatedFileName = $translatedDirName . substr($file, strlen($localePath));
            if (!file_exists(dirname($translatedFileName))) {
                mkdir(dirname($translatedFileName), 0777, true);
            }
            file_put_contents($translatedFileName, $data);
        }

        if (!$outputSeparate) {
            $csv->saveData($this->_outputDirName, $resultData);
        }

        $this->_addMessage(MESSAGE_TYPE_NOTICE, 'Translation created successfully');
    }

    protected function separateTranslations($inpuArray)
    {
        $resultArray = array();
        foreach($inpuArray as $translateLine) {
            $resultArray[$translateLine[0]]['from'][] = $translateLine[1];
            $resultArray[$translateLine[0]]['to'][] = $translateLine[2];
        }
        return $resultArray;
    }

    protected function _fromCsvToTemplate()
    {
        $inputSeparate = is_dir($this->_translateName);

        if (!$inputSeparate) {
            $translatedDirName = dirname($this->_translateName) . DS . $this->_localeName.'_templates';
        } else {
            $translatedDirName = $this->_translateName . $this->_localeName.'_templates';
        }

        $files = $this->_getFilesToProcess($translatedDirName);
        $csv = new Magento_File_Csv();
        if (!$inputSeparate) {
            $strings = $csv->getData($this->_translateName);
            $strings = $this->separateTranslations($strings);
        }

        foreach ($files as $alias=>$file){
            $template = file_get_contents($file);

            if ($inputSeparate) {
                $strings = $csv->getData($this->_translateName . $alias . 'csv');
                array_merge(array($alias), $strings);
                $stringsArray = $this->separateTranslations($strings);
            }

            $stringsArray = $strings[$alias];
            $template = str_replace('__(\'' . $stringsArray['from'] . '\')__', $stringsArray['to'], $template);

            $translatedFileName = $translatedDirName . substr($file, strlen($translatedDirName));

            file_put_contents($this->_outputDirName . DS . $translatedFileName, $template);
            break;
        }

        $this->_addMessage(MESSAGE_TYPE_NOTICE, 'Templates created successfully');
    }

    protected function splitTranslation()
    {
        /*
        $this->_arguments = array(
            'inputName' => $inputName,
        );*/
        $csv = new Magento_File_Csv();
        $inputData = $csv->getData($this->_arguments['inputName']);
        $output = array();
        foreach ($inputData as $row){
            $output[$row[0]][] = array_slice($row, 1);
        }
        $resultDir = dirname($this->_arguments['inputName']) . DS
            . pathinfo($this->_arguments['inputName'], PATHINFO_FILENAME) . DS;

        if (!file_exists($resultDir)) {
            mkdir($resultDir, 0777, true);
        }

        foreach ($output as $file=>$data){
            $outputFileName = $resultDir . "{$file}.csv";
            $csv->saveData($outputFileName, $data);
        }

        $this->_addMessage(MESSAGE_TYPE_NOTICE, 'Translation splitted successfully');
    }

    protected function mergeLocales()
    {
        /*
        $this->_arguments = array(
            'inputName1' => $inputName1,
            'inputName2' => $inputName1,
            'outputName' => $outputName,
        );*/

        $csv = new Magento_File_Csv();
        $strings1 = $this->separateTranslations($csv->getData($this->_arguments['inputName1']));
        $strings2 = $this->separateTranslations($csv->getData($this->_arguments['inputName2']));
        $resultArray = array();
        $skippedLines = 0;
        $skippedFiles = '';

        foreach($strings1 as $alias => $row) {
            if (isset($strings2[$alias])) {
                for ($i = 0, $j=0, $c = count($strings1[$alias]['from']); $i<$c; $i++) {
                    if (isset($strings2[$alias]['to'][$j])) {
                        $resultArray[] = array(
                            $alias,
                            $strings1[$alias]['from'][$i],
                            $strings2[$alias]['to'][$j]
                        );
                        $j++;
                    } else {
                        $skippedLines++;
                    }
                }
            } else {
                $skippedFiles .= ' ' . $alias;
            }
        }
        $csv->saveData($this->_arguments['outputName'], $resultArray);

        $this->_addMessage(MESSAGE_TYPE_NOTICE, 'Translation merged successfully');
        $this->_addMessage(MESSAGE_TYPE_NOTICE, 'Skipped sections: ' . $skippedFiles);
        $this->_addMessage(MESSAGE_TYPE_NOTICE, 'Skipped strings: ' . $skippedLines);
    }

    /**
     * Generate process
     *
     * @return bool
     */
    public function run()
    {
        if ($this->_error) {
            return false;
        }

        switch ($this->_action) {
            case ACTION_SPLIT:
                $this->splitTranslation();
                break;

            case ACTION_MERGE_LOCALES:
                $this->mergeLocales();
                break;

            default:
                if ($this->_translateName) {
                    $this->_fromCsvToTemplate();
                } else {
                    $this->_fromTemplateToCsv();
                }
                break;
        }

        return true;
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

    protected function _addMessage($type, $message)
    {
        $this->_messages[] = array('type'=>$type, 'text'=>$message);
    }
}

$generate = new Generate($argv);
$generate->run();
echo $generate->renderMessages();
echo "\n\n";
