<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bimathew
 * Date: 8/24/13
 * Time: 10:42 AM
 * To change this template use File | Settings | File Templates.
 */

class addSlash
{
    const FOUR_SPACES = '    ';
    private $path = null;
    private $namespace = array();
    private $splitLine = 0;
    private $reservedKeyWords = array(
        'Abstract',
        'Interface',
        'Class',
        'Array',
        'Exception',
        'Default',
        'List',
        'Global',
        'Declare'
    );
    private $reserveCheck = false;
    private $fileMapper = array();
    private $renameFileLogger = "renameFile.txt";
    private $renameClassLogger = "renameClass.txt";
    private $errorLog = "error.txt";
    private $fileChanged = array();
    private $rootDirectory = null;
    private $braceStarted=false;

    public function  __construct($dir)
    {
        $this->scanFile($this->getFiles($dir));
    }

    public function getFiles($directory)
    {
        $files = array();
        if (is_dir($directory)) {
            $rdi = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
            foreach (new \RecursiveIteratorIterator($rdi) as $file) {
                /** @var $file \SplFileInfo */
                if ($file->getExtension() != 'php') {
                    continue;
                }
                $files[] = $file->getRealPath();
            }
        } elseif (is_file($directory)) {
            $files[] = $directory;
        }
        return $files;
    }


    private function scanFile($fileArray)
    {
        foreach ($fileArray as $file) {
            $lines = file($file);
            $parsedLine = null;
            $count = 0;
            $starBraceCheck = false;
            echo "$file psr1 process started \n";
            foreach ($lines as $line) {
                $trimLine = trim($line);
                if ($this->compareInString($trimLine, 0, 5, 'class') || $this->compareInString(
                        $trimLine,
                        0,
                        7,
                        'extends'
                    ) || $this->compareInString($trimLine, 0, 10, 'implements')
                ) {
                    if ($this->compareInString($line, 0, 5, 'class')) {
                        $this->splitLine = $count;
                        $starBraceCheck = true;
                    }
                    $parsedLine = $this->scanClass($line, $parsedLine, $file);
                } else {
                    if ($this->compareInString($line, 0, 14, 'abstract class')) {
                        $this->splitLine = $count;
                        $starBraceCheck = true;
                        $parsedLine = $this->scanClass($line, $parsedLine, $file);
                    } else {
                        if ($this->compareInString($line, 0, 9, "interface")) {
                            $starBraceCheck = true;
                            $this->splitLine = $count;
                            $parsedLine = $this->scanClass($line, $parsedLine, $file);
                        } else {
                            if ($this->compareInString($line, 0, 11, "final class")) {
                                $starBraceCheck = true;
                                $this->splitLine = $count;
                                $parsedLine = $this->scanClass($line, $parsedLine, $file);
                            } else {
                                if ($trimLine == '{') {
                                    $this->braceStarted = true;
                                    $parsedLine[] = $line;
                                } else {
                                    if ($this->braceStarted == false && $starBraceCheck == true) {
                                        $parsedLine = $this->scanClass($line, $parsedLine, $file, true);
                                        } else {
                                        $parsedLine[] = $line;
                                    }
                                }
                            }
                        }
                    }
                }
                $count++;

            }
            $this->makeFile($file, $parsedLine);
            echo "$file PSR1 transformation completed \n";


        }
    }

    private function scanClass($line, $array, $file, $parseFlag = false)
    {

        $exp = explode(" ", $line);
        $parse = $parseFlag;
        $string = "";
        $this->reservecheck = false;
        $count = 0;

        foreach ($exp as $value) {
            clearstatcache();
            $val = trim(strtolower($value));
            if ($this->isComment($val)) {
                $tempArr = array_slice($exp, $count + 1, count($exp));
                $newString = implode(" ", $tempArr);
                $string = $string . $newString;
                break;
            }
            if (trim(
                    $val
                ) == '' || $val === 'abstract' || $val === 'class' || $val === 'final' || $val === 'interface' || $val === 'extends' || $val === 'implements'
                || $val=='{'  || $val=='}'  || $val=='{}' ) {
                if($val='{'){
                    $this->braceStarted = true;
                }
                $parse = true;
                if ($val === 'abstract' || $val === 'class' || $val === 'final' || $val === 'interface'||$val=='{'  || $val=='}'  || $val=='{}') {
                    $this->reserveCheck = true;

                } else {
                    $this->reserveCheck = false;
                }
                if (trim($val) == '') {
                    $string = $string . ' ';

                } else {
                    $string = $string . $value . " ";
                }
                continue;
            }

            if ($parse) {
                $vals = explode(",", $value);

                $multipleImplements = false;
                if (count($vals) > 1) {
                    $multipleImplements = true;
                }

                foreach ($vals as $val) {
                    //fix for the global scanner
                    $val=trim($val);
                    if(!empty($val)){
                        if ($this->reserveCheck) {
                            $string = $string . trim($val) . " ";
                        } else {
                            $newClass = str_replace("\\\\", "\\", $val);
                            $newClass = trim(str_replace("\\", " ", $newClass));
                            $newClass = str_replace(" ", "\\", $newClass);
                            if ($multipleImplements && trim($val) != trim($vals[count($vals) - 1])) {
                                $string = $string . "\\".$newClass . ",";
                            } else {
                                $string = $string . "\\".$newClass . " ";
                            }


                        }
                    }

                }
            }

            $count++;
        }

        $string = rtrim($string) . "\n";
        $array[] = $string;
        return $array;
    }


    private function isComment($str)
    {
        $str = trim($str);
        $first_two_chars = substr($str, 0, 2);
        $last_two_chars = substr($str, -2);
        return $first_two_chars == '//' || substr(
            $str,
            0,
            1
        ) == '#' || ($first_two_chars == '/*' && $last_two_chars == '*/');
    }

    private function compareInString($line, $start, $end, $compare)
    {
        return (substr($line, $start, $end) === $compare);
    }


    private function makeFile($file, $array)
    {

        $string = "";
        // new line feeder
        if (end($array) == "}") {
            $array[] = "\n";
        }
        foreach ($array as $key) {
            $string = $string . $key;
        }
        $string=str_replace('Mage::',"\\Mage::",$string);
            file_put_contents($file, $string);

    }
}

if (isset($argv[1])) {
    $src = explode("=", $argv[1]);
    if (isset($src[1])) {
        $src = trim($src[1]);

    } else {
        throw new exception("src paramter cannot be empty");
    }

    $PSRX = new addSlash($src);
} else {
    echo "Please provide the arguments";
}