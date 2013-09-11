<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

//merge

//- original file
//- translate file
//- write to file non translate
namespace Magento\Tools\Translate;

define('BASE_PATH', dirname(dirname(dirname(dirname(dirname(__DIR__))))));
define('DS', DIRECTORY_SEPARATOR);

class Merge
{
    protected $_usage;
    protected $_args;
    /**
     * Magento File CSV
     *
     * @var \Magento\File\Csv
     */
    protected $_csv;

    protected $_translate = array();

    public function __construct()
    {
        $this->_usage = '
USAGE:
-------------------------------------------------------------------------------
$> php -f Merge.php -- --of originalFile.csv --tf translateFile.csv --mf resultFile.csv
-------------------------------------------------------------------------------

OPTIONAL PARAMETRS:
-------------------------------------------------------------------------------
--diff      add column with diff result (true or false)
-------------------------------------------------------------------------------

';

        $this->_checkArgs();
        require_once(BASE_PATH . DS . 'lib' . DS . 'Magento' . DS . 'File' . DS . 'Csv.php');
        $this->_csv = new \Magento\File\Csv();
    }

    protected function _getArgs()
    {
        if (is_null($this->_args)) {
            $this->_args = array();
            $argCurrent = null;
            foreach ($_SERVER['argv'] as $arg) {
                if (preg_match('/^--(.*)$/', $arg, $match)) {
                    $argCurrent = $match[1];
                    $this->_args[$argCurrent] = true;
                }
                else {
                    if ($argCurrent) {
                        $this->_args[$argCurrent] = $arg;
                    }
                }
            }
        }
    }

    protected function _checkArgs()
    {
        $this->_getArgs();

        if (!isset($this->_args['of'])) {
            $this->_exception("Please indicate original file");
        }
        if (!file_exists($this->_args['of'])) {
            $this->_exception("Original file '%s' is not exists", $this->_args['of']);
        }
        if (!isset($this->_args['tf'])) {
            $this->_exception("Please indicate translate file");
        }
        if (!file_exists($this->_args['tf'])) {
            $this->_exception("Translate file '%s' is not exists", $this->_args['tf']);
        }
        if (!isset($this->_args['mf'])) {
            $this->_exception("Please indicate result file");
        }
        $dir = dirname($this->_args['mf']);
        if (!is_writeable($dir)) {
            $this->_exception("Output dir '%s' isn\'t writeable", realpath($dir));
        }
    }

    protected function _exception($message)
    {
        $inputArgs = func_get_args();
        array_shift($inputArgs);
        if ($inputArgs) {
            $message = vsprintf($message, $inputArgs);
        }

        throw new \Exception($this->_usage . $message . "\n\n");
    }

    protected function _findTranslate($string, $module)
    {
        if (isset($this->_translate[$module][$string])) {
            return $this->_translate[$module][$string];
        }
        else {
            foreach ($this->_translate as $translate) {
                if (isset($translate[$string])) {
                    return $translate[$string];
                }
            }
        }
        return false;
    }

    public function run()
    {
        $result  = array(
            'original_modules'  => array(),
            'original_string'   => 0,
            'translate_modules' => array(),
            'translate_string'  => 0,
            'translated'        => 0,
            'diff_string'       => 0
        );

        $outData = array();
        $outKey  = 0;
        foreach ($this->_csv->getData($this->_args['tf']) as $data) {
            if (empty($data[0]) || empty($data[1]) || empty($data[2])) {
                continue;
            }
            $this->_translate[$data[0]][$data[1]] = $data[2];
            $result['translate_modules'][$data[0]] = isset($result['translate_modules'][$data[0]]) ? $result['translate_modules'][$data[0]] + 1 : 1;
            $result['translate_string'] ++;
        }

        foreach ($this->_csv->getData($this->_args['of']) as $data) {
            $result['original_modules'][$data[0]] = isset($result['original_modules'][$data[0]]) ? $result['original_modules'][$data[0]] + 1 : 1;
            $result['original_string'] ++;
            $translate = $this->_findTranslate($data[1], $data[0]);
            $outData[$outKey] = array(
                $data[0],
                $data[1],
                $translate ? $translate : $data[2]
            );
            if (isset($this->_args['diff'])) {
                $outData[$outKey][3] = $translate ? 'true' : 'false';
            }
            if ($translate) {
                $result['translated'] ++;
            }
            else {
                $result['diff_string'] ++;
            }
            $outKey ++;
        }
        if (file_exists($this->_args['mf'])) {
            @unlink($this->_args['mf']);
        }
        $this->_csv->saveData($this->_args['mf'], $outData);

        print 'RESULT
-------------------------------------------------------------------------------
modules on translate file:  '.count($result['translate_modules']).'
string on translate file:   '.$result['translate_string'].'
modules on original file:   '.count($result['original_modules']).'
string on original file:    '.$result['original_string'].'

translated string:          '.$result['translated'].'
unknown string:             '.$result['diff_string'].'
';
    }
}

try {
    $merge = new \Magento\Tools\Translate\Merge();
    $merge->run();
}
catch (\Exception $e) {
    die($e->getMessage());
}
