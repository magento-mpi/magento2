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

define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', dirname(dirname(dirname(dirname(dirname(__DIR__))))));

require_once BASE_PATH . DS . 'lib' . DS . 'Zend/Exception.php';
require_once BASE_PATH . DS . 'lib' . DS . 'Zend/Console/Getopt/Exception.php';
require_once BASE_PATH . DS . 'lib' . DS . 'Zend/Console/Getopt.php';

class MultyGetopt extends \Zend_Console_Getopt {

    protected function _parseSingleOption($flag, &$argv)
    {
            if ($this->_getoptConfig[self::CONFIG_IGNORECASE]) {
                $flag = strtolower($flag);
            }
            if (!isset($this->_ruleMap[$flag])) {
                throw new \Zend_Console_Getopt_Exception(
                    "Option \"$flag\" is not recognized.",
                    $this->getUsageMessage());
            }
            $realFlag = $this->_ruleMap[$flag];
            switch ($this->_rules[$realFlag]['param']) {
                case 'required':
                    if (count($argv) > 0) {
                        $param = array_shift($argv);
                        $this->_checkParameterType($realFlag, $param);
                    } else {
                        throw new \Zend_Console_Getopt_Exception(
                            "Option \"$flag\" requires a parameter.",
                            $this->getUsageMessage());
                    }
                    break;
                case 'optional':
                    if (count($argv) > 0 && substr($argv[0], 0, 1) != '-') {
                        $param = array_shift($argv);
                        $this->_checkParameterType($realFlag, $param);
                    } else {
                        $param = true;
                    }
                    break;
                default:
                    $param = true;
            }

            if(isset($this->_options[$realFlag])){
                if(!is_array($this->_options[$realFlag])) {
                    $tmp = $this->_options[$realFlag];
                    $this->_options[$realFlag]=array();
                    array_push($this->_options[$realFlag],$tmp);
                }
                array_push($this->_options[$realFlag],$param);
            } else {
                $this->_options[$realFlag] = $param;
            }


    }



}

?>
