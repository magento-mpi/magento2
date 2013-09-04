<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bimathew
 * Date: 8/22/13
 * Time: 11:15 AM
 * To change this template use File | Settings | File Templates.
 */

require __DIR__ . '/../../../../../app/autoload.php';
Magento\Autoload\IncludePath::addIncludePath(__DIR__ . '/../../../../../lib');

class namespacer
{

    const FOUR_SPACES = '    ';
    private $path = null;
    private $namespace = array();
    private $splitLine = 0;
    private $requireOnce = 0;
    private $reservedKeyWords = array(
        'Abstract',
        'Interface',
        'Class',
        'Array',
        'Exception',
        'Default',
        'List',
        'Global',
        'Declare',
        'Extends'
    );
    private $reserveCheck = false;
    private $fileMapper = array();
    private $renameFileLogger = "nmrenameFile.txt";
    private $renameClassLogger = "nmrenameClass.txt";
    private $globalScanner = "globalscanner.txt";
    private $errorLog = "error.txt";
    private $fileChanged = array();
    private $rootDirectory = null;
    private $classSearch = array();
    private $classReplace = array();
    private $allowedFileExtensions = array('php', 'phtml', 'html', 'xml', 'sql');
    private $ignoreFile = "blacklist.txt";
    private $blackListArray = array();
    private $testDir=array();
    private $addSlashArray = array(
        "Zend_",
        "Twig_",
        "Apache_Solrs",
        "PHPUnit_",
        "ArrayIterator",
        "CentinelClient",
        "Countable",
        "Exception",
        "InvalidArgumentException",
        "IteratorAggregate",
        "LogicException",
        "ReflectionClass",
        "ReflectionMethod",
        "DOMDocument",
        "DOMXPath",
        "BadMethodCallException",
        "PDO",
        "ArrayAccess",
        "SimpleXMLElement",
        "RecursiveDirectoryIterator",
        "Mage::",
        "Zend\\\\",
        "DOMException",
        "DOMNode",
        "RecursiveIteratorIterator",
        "DOMNodeList",
        "DOMElement",
        "stdClass",
        "ErrorException",
        "COMPersistHelper",
        "DateTime",
        "DateTimeZone",
        "DateInterval",
        "DatePeriod",
        "BadFunctionCallException",
        "DomainException",
        "LengthException",
        "OutOfRangeException",
        "RuntimeException",
        "OutOfBoundsException",
        "OverflowException",
        "RangeException",
        "UnderflowException",
        "UnexpectedValueException",
        "IteratorIterator",
        "FilterIterator",
        "RecursiveFilterIterator",
        "ParentIterator",
        "LimitIterator",
        "CachingIterator",
        "RecursiveCachingIterator",
        "NoRewindIterator",
        "AppendIterator",
        "InfiniteIterator",
        "RegexIterator",
        "RecursiveRegexIterator",
        "EmptyIterator",
        "RecursiveTreeIterator",
        "ArrayObject",
        "RecursiveArrayIterator",
        "SplFileInfo",
        "DirectoryIterator",
        "FilesystemIterator",
        "GlobIterator",
        "SplFileObject",
        "SplTempFileObject",
        "SplDoublyLinkedList",
        "SplQueue",
        "SplStack",
        "SplHeap",
        "SplMinHeap",
        "SplMaxHeap",
        "SplPriorityQueue",
        "SplFixedArray",
        "SplObjectStorage",
        "MultipleIterator",
        "ReflectionException",
        "Reflection",
        "ReflectionFunctionAbstract",
        "ReflectionFunction",
        "ReflectionParameter",
        "ReflectionObject",
        "ReflectionProperty",
        "ReflectionExtension",
        "php_user_filter",
        "Directory",
        "ZipArchive",
        "LibXMLError",
        "DOMStringList",
        "DOMNameList",
        "DOMImplementationList",
        "DOMImplementationSource",
        "DOMImplementation",
        "DOMNameSpaceNode",
        "DOMDocumentFragment",
        "DOMNamedNodeMap",
        "DOMCharacterData",
        "DOMAttr",
        "DOMText",
        "DOMComment",
        "DOMTypeinfo",
        "DOMUserDataHandler",
        "DOMDomError",
        "DOMErrorHandler",
        "DOMLocator",
        "DOMConfiguration",
        "DOMCdataSection",
        "DOMDocumentType",
        "DOMNotation",
        "DOMEntity",
        "DOMEntityReference",
        "DOMProcessingInstruction",
        "DOMStringExtend",
        "Iterator",
        "PDOException",
        "PDOStatement",
        "PDORow",
        "SimpleXMLIterator",
        "XMLReader",
        "XMLWriter",
        "PharException",
        "Phar",
        "PharData",
        "PharFileInfo",
        "SoapClient",
        "SoapVar",
        "SoapServer",
        "SoapFault",
        "SoapParam",
        "SoapHeader"
    );
    private $gitShell = null;


    private $xmlFile=array();
    private $phpFile=array();

    public function __construct($path, $rootDirectory, $tesDir = false)
    {


        $this->rootDirPath = realpath(__DIR__);
        $this->path = $path;
        $this->gitShell = new Magento\Shell(null);
        $this->gitClassMove();
        $this->gitListPackageMove();
        $this->rootDirectory = $rootDirectory;
        if($tesDir){
            $this->testDir=$path;
        }
        $this->renameFileLogger = time() . $this->renameFileLogger;
        $this->renameClassLogger = time() . $this->renameClassLogger;
        $this->globalScanner = time() . $this->globalScanner;
        if (file_exists($this->ignoreFile)) {
            $temp = file($this->ignoreFile);
            foreach ($temp as $fl) {
                if (!empty($fl)) {
                    $array = $this->scanDirectory(trim($fl), false);
                    $this->blackListArray = array_merge($this->blackListArray, $array);
                }

            }
        }
    }

    public function gitClassMove()
    {
        $params = array(
            '../../../../../app/code/Magento/Tax/Model/Class',
            '../../../../../' . 'app/code/Magento/Tax/Model/TaxClass'
        );

        try {
            if (realpath($params[0]) && !realpath($params[1])) {
                echo "git-moving $params[0] to $params[1]\n";
                $this->gitShell->execute(
                    'git mv %s %s',
                    $params
                );
                $RealPath1 = realpath($params[1]);
                $files1 = $this->scanDirectory($RealPath1);
                if (!empty($files1)) {
                    foreach ($files1 as $key) {
                        $pattern1 = 'Magento_Tax_Model_Class_';
                        $rep1 = 'Magento_Tax_Model_TaxClass_';

                        $cont = str_replace($pattern1, $rep1, file_get_contents($key));
                        file_put_contents($key, $cont);
                    }

                }
            } else {
                echo "skipping already-moved $params[0]\n";
            }

            $params = array(
                '../../../../../app/code/Magento/Tax/Model/Resource/Class',
                '../../../../../' . 'app/code/Magento/Tax/Model/Resource/TaxClass'
            );
            //git mv %s %s',
            if (realpath($params[0]) && !realpath($params[1])) {
                echo "git-moving $params[0] to $params[1]\n";
                $this->gitShell->execute(
                    'git mv %s %s',
                    $params
                );
                $RealPath = realpath($params[1]);
                $files = $this->scanDirectory($RealPath);
                if (!empty($files)) {
                    foreach ($files as $key) {
                        $pattern = 'Magento_Tax_Model_Resource_Class_';
                        $rep = 'Magento_Tax_Model_Resource_TaxClass_';

                        $cont = str_replace($pattern, $rep, file_get_contents($key));
                        file_put_contents($key, $cont);
                    }

                }
            } else {
                echo "skipping already-moved $params[0]\n";
            }
        } catch (Exception $e) {
            $string = 'Message: ' . $e->getMessage() . "\n";
            $this->logFile($this->errorLog, $string);
        }

    }

    /**
     * rename the List package
     * Magento\Core\Model\Layout\File\List
     */
    public function gitListPackageMove()
    {
        $params = array(
            '../../../../../' . 'app/code/Magento/Core/Model/Layout/File/List',
            '../../../../../' . 'app/code/Magento/Core/Model/Layout/File/ListFile',
        );

        try {
            if (realpath($params[0]) && !realpath($params[1])) {
                echo "git-moving $params[0] to $params[1]\n";
                $this->gitShell->execute(
                    'git mv %s %s',
                    $params
                );
                $RealPath1 = realpath($params[1]);
                $files1 = $this->scanDirectory($RealPath1);
                if (!empty($files1)) {
                    foreach ($files1 as $key) {
                        $pattern1 = 'Magento_Core_Model_' . 'Layout_File_List_';
                        $rep1 = 'Magento_Core_Model_' . 'Layout_File_ListFile_';

                        $cont = str_replace($pattern1, $rep1, file_get_contents($key));
                        file_put_contents($key, $cont);
                    }

                }
            } else {
                echo "skipping already-moved $params[0]\n";
            }

            $params = array(
                '../../../../../dev/tests/unit/testsuite/Magento/Core/Model/Layout/File/List',
                '../../../../../dev/tests/unit/testsuite/Magento/Core/Model/Layout/File/ListFile'
            );
            //git mv %s %s',
            if (realpath($params[0]) && !realpath($params[1])) {
                echo "git-moving $params[0] to $params[1]\n";
                $this->gitShell->execute(
                    'git mv %s %s',
                    $params
                );
                $RealPath = realpath($params[1]);
                $files = $this->scanDirectory($RealPath);
                if (!empty($files)) {
                    foreach ($files as $key) {
                        $pattern = 'Magento_Core_Model' . '_Layout_File_List_';
                        $rep = 'Magento_Core_Model' . '_Layout_File_ListFile_';

                        $cont = str_replace($pattern, $rep, file_get_contents($key));
                        file_put_contents($key, $cont);
                    }

                }
            } else {
                echo "skipping already-moved $params[0]\n";
            }
        } catch (Exception $e) {
            $string = 'Message: ' . $e->getMessage() . "\n";
            $this->logFile($this->errorLog, $string);
        }

    }

    public function logFile($logFile, $string)
    {
        file_put_contents($logFile, $string, FILE_APPEND);
        clearstatcache();
    }

    public function convertToPSRX()
    {
        clearstatcache();
        $files = $this->scanDirectory($this->path);
        if (count($files)) {
            $this->scanFile($files);
        } else {

            throw new exception("Files cannot be processed");
        }
    }


    protected function scanDirectory($path, $onlyPhp = true, $blackList = false)
    {
        $files = array();
        clearstatcache();
        if (is_dir($path)) {
            $rdi = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
            foreach (new \RecursiveIteratorIterator($rdi) as $file) {
                /** @var $file \SplFileInfo */
                if ($onlyPhp) {
                    if ($file->getExtension() != 'php') {
                        continue;
                    }
                } else {
                    if (!in_array($file->getExtension(), $this->allowedFileExtensions)) {
                        continue;
                    }
                }
                if ($blackList) {
                    if (!in_array($file->getRealPath(), $this->blackListArray)) {
                        $files[] = $file->getRealPath();
                    }
                } else {
                    $files[] = $file->getRealPath();
                }


            }
        } elseif (is_file($path)) {
            $files[] = $path;
        }
        return $files;
    }

    private function compareInString($line, $start, $end, $compare)
    {
        return (substr($line, $start, $end) === $compare);
    }


    private function scanFile($fileArray)
    {
        foreach ($fileArray as $file) {
            $info = new SplFileInfo($file);
            if ($info->getExtension() != 'php') {
                echo "Only php files can be used for namespace formatting \n";
                continue;
            }

            /* if(!$this->checkClassFile($file)){
                 echo "$file Not a clas file \n";
                 continue;
             }*/

            clearstatcache();
            $lines = file($file);
            $parsedLine = null;
            $this->requireOnce = 0;
            $this->namespace = array();
            $count = 0;
            echo "$file psr1 process started \n";
            foreach ($lines as $line) {
                $trimLine = trim($line);
                if ($this->compareInString($trimLine, 0, 12, 'require_once')) {
                    $this->requireOnce = $count;
                    $parsedLine[] = $line;
                } else {
                    if ($this->compareInString($trimLine, 0, 5, 'class')
                    ) {
                        if ($this->compareInString($line, 0, 5, 'class')) {
                            $this->splitLine = $count;
                        }
                        $parsedLine = $this->scanClass($line, $parsedLine, $file);
                    } else {
                        if ($this->compareInString($line, 0, 14, 'abstract class')) {
                            $this->splitLine = $count;

                            $parsedLine = $this->scanClass($line, $parsedLine, $file);
                        } else {
                            if ($this->compareInString($line, 0, 9, "interface")) {

                                $this->splitLine = $count;
                                $parsedLine = $this->scanClass($line, $parsedLine, $file);
                            } else {
                                if ($this->compareInString($line, 0, 11, "final class")) {

                                    $this->splitLine = $count;
                                    $parsedLine = $this->scanClass($line, $parsedLine, $file);
                                } else {
                                    $parsedLine[] = $line;

                                }
                            }
                        }
                    }
                }

                $count++;

            }
            $this->makeFile($file, $parsedLine, $this->namespace);
            echo "$file PSR1 transformation completed \n";


        }
        echo "=====================\n";
        echo "Started Global Scanning \n";
        $this->globalClassnameScanner();
        echo "=====================\n";
        echo "Finished Global Scanning \n";

        $this->replaceThirdParty($this->path);
        echo "Finished Third Party  Replacement \n";

        echo "=====================\n";
        echo "Started Started Sanity check Cleanup \n";
        $this->sanityCheckCleanup();
    }

    private function sanityCheckCleanup()
    {

        $sanitySearchXML = array("type name=\"\\Magento\\", "preference for=\"\\Magento",
            "module name=\"\\Magento\\","type=\"\\Magento\\");
        $sanityReplaceXMl=array("type name=\"Magento\\", "preference for=\"Magento",
            "module name=\"Magento_","type=\"Magento\\");

        echo "=====================\n";
        echo "XML Sanity check  Started\n";
        foreach($this->xmlFile as $key){
            $contentsXml=str_replace($sanitySearchXML,$sanityReplaceXMl,file_get_contents($key));
            file_put_contents($key, $contentsXml);
        }
        echo "XML Sanity check Completed \n";

        echo "=====================\n";
        echo "php Sanity check  Started\n";
        $sanityPhpSearch = array("['\\Magento\\", "get('\\Magento\\","\\\\Magento");
        $sanityphpReplace = array("['Magento\\", "get('Magento\\","\\Magento");
        echo "php Sanity check  Completed\n";

        foreach($this->phpFile as $key){
            $contentsPhp=str_replace($sanityPhpSearch,$sanityphpReplace,file_get_contents($key));
            file_put_contents($key, $contentsPhp);
        }


    }
    private function globalClassnameScanner()
    {
        clearstatcache();
        if (is_dir($this->rootDirectory) && !empty($this->classSearch) & !empty($this->classReplace)) {
            $files = $this->scanDirectory($this->rootDirectory, false, true);
            $search = array();
            foreach ($this->classSearch as $searchKey) {
                $search[] = "/\\" . $searchKey . "\\b/";
            }

            if (count($search) === count($this->classReplace) && count($search) === count($this->classSearch)) {
                $this->classSearch = $search;
                foreach ($files as $file) {
                    $ext=strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if($ext=='xml'){
                        $this->xmlFile[]=$file;
                    }
                    if($ext=='php'||$ext=='phtml'){
                        $this->phpFile[]=$file;
                    }

                    $this->logFile($this->globalScanner, $file . "Start Processing \n");
                    clearstatcache();
                    //$contents=str_replace($this->classSearch,$this->classReplace,file_get_contents($file));
                    $contents = preg_replace($this->classSearch, $this->classReplace, file_get_contents($file));
                    file_put_contents($file, $contents);
                    $contents=str_replace("\\\\Magento\\","\\Magento\\",$contents);
                    $this->logFile($this->globalScanner, $file . "Scanning completed \n");
                }
            } else {
                $string = "Cannot do a global scan and replacement , Error Please do check the rename class and rename files" . "\n";
                $this->logFile($this->errorLog, $string);
                echo "Check Error Log \n";
            }


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
                || $val == '{' || $val == '}' || $val == '{}'
            ) {
                $parse = true;
                if ($val === 'abstract' || $val === 'class' || $val === 'final' || $val === 'interface') {
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
                    $val = trim($val);
                    if ($this->reserveCheck) {
                        $val = str_replace("\\\\", "\\", $val);
                        $val = trim(str_replace("\\", " ", $val));
                        $val = str_replace(" ", "_", $val);
                        $val = str_replace("\\", "_", $val);
                        $namespace =
                            "namespace " . str_replace(
                                '_',
                                "\\",
                                substr($val, 0, strrpos($val, '_'))
                            ) . ';' . "\n" . "\n";
                        $namespaceCheck = trim(str_replace('namespace ;', '', $namespace));
                        if ($namespaceCheck) {
                            $this->namespace[] = $namespace;
                        } else {

                            $this->fileChanged[] = $file;
                        }

                        if (strpos($val, '_') !== false) {
                            $newClass = substr($val, strrpos($val, '_') + 1);
                        } else {
                            $newClass = $val;
                        }
                        if ((in_array(trim($newClass), $this->reservedKeyWords)) && $namespaceCheck) {
                            $newClass = $this->setMapReservedFiles(
                                trim($newClass),
                                $namespace,
                                $file,
                                $this->reserveCheck
                            );
                            $this->reserveCheck = false;

                        } else {
                            if (in_Array($file, $this->reservedKeyWords)) {
                                $baseFileName = basename($file);
                                $newClass = trim($newClass); // $file is set to "index.php";
                                if ($baseFileName != $newClass && !empty($newClass)) {
                                    $newFileName = dirname($file) . "\\" . $newClass . '.php';
                                    $this->fileMapper[$file] = $newFileName;
                                }
                            }


                        }
                        $change = "\\" . str_replace(
                                "_",
                                "\\",
                                trim(
                                    str_replace(
                                        "\\",
                                        "_",
                                        str_replace(
                                            ';',
                                            '',
                                            trim(str_replace('namespace', '', $namespace))
                                        ) . "\\" . trim($newClass)
                                    )
                                )
                            );
                        if ((trim($val) !== 'implements') || (trim($val) !== 'extends')) {
                            $val = str_replace('//', '', trim($val));
                            $change = str_replace('//', '', trim($change));
                            $val = str_replace("\\\\", "\\", trim($val));
                            $change = str_replace("\\\\", "\\", trim($change));

                            $mess = trim($val) . "  =>  " . $change . "\n";
                            $this->classSearch[] = trim($val);
                            $this->classReplace[] = $change;
                            $this->logFile($this->renameClassLogger, $mess);
                        }

                    } else {

                        $newClass = trim($val);

                    }
                    $newClass = str_replace("\\\\", "\\", $newClass);

                    if ($multipleImplements && trim($val) != trim($vals[count($vals) - 1])) {
                        $string = $string . $newClass . ",";
                    } else {
                        $string = $string . $newClass . " ";
                    }

                }
            }
            $count++;
        }
        if (substr($string, -1) == '\\') {
            $string = substr($string, 0, -1);
        }

        $string = rtrim($string) . "\n";
        $array[] = $string;
        return $array;
    }


    private function setMapReservedFiles($newClass, $nameSpace, $file, $reserveCheck)
    {
        clearstatcache();
        $newClass = trim($newClass);
        $string = explode("\\", trim($nameSpace));
        if (count($string) == 1) {
            $string = explode(" ", $string[0]);

        }
        if ($newClass === 'Exception' || $newClass === 'Trait' || $newClass === 'Interface') {
            $newClass = trim(str_replace(";", '', ($string[count($string) - 1]))) . $newClass;
        } else {
            $newClass = $newClass . trim(str_replace(";", '', ($string[count($string) - 1])));
        }

        if ($reserveCheck) {
            $newFileName = dirname($file) . "\\" . $newClass . '.php';
            $this->fileMapper[$file] = $newFileName;
        }
        return $newClass;
    }

    private function renameReservedFileNames($file)
    {
        if (isset($this->fileMapper[$file])) {
            clearstatcache();

            try {
                if (!empty($this->rootDirPath)) {
                    $path = $this->getRelativePath($this->rootDirPath, $file);
                    $tar = $this->getRelativePath($this->rootDirPath, $this->fileMapper[$file]);
                    $this->gitRename($path, $tar);
                    $string = $file . " =>  " . $this->fileMapper[$file] . "\n";
                    $this->logFile($this->renameFileLogger, $string);
                } else {
                    $this->gitRename($file, $this->fileMapper[$file]);
                    $string = $file . " =>  " . $this->fileMapper[$file] . "\n";
                    $this->logFile($this->renameFileLogger, $string);
                }


            } catch (Exception $e) {
                $string = 'Message: ' . $e->getMessage() . "\n";
                $this->logFile($this->errorLog, $string);
            }

        }

    }

    private function makeFile($file, $array, $namespace)
    {

        $first_Array = (array_slice($array, 0, $this->splitLine));
        $key_split = (array_slice($array, $this->splitLine));
        $array = array_merge($first_Array, $namespace, $key_split);

        $string = "";
        // new line feeder
        if (end($array) == "}") {
            $array[] = "\n";
        }
        if ($this->requireOnce !== 0) {
            $namespace = $array[$this->splitLine];
            $requireOnce = $array[$this->requireOnce];
            $array[$this->requireOnce] = $namespace;
            $array[$this->splitLine] = $requireOnce;
        }
        foreach ($array as $key) {
            $string = $string . $key;
        }

        if (!in_array($file, $this->fileChanged)) {
            clearstatcache();
            file_put_contents($file, $string);
            $this->renameReservedFileNames($file);
        }


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

    public function replaceThirdParty($path)
    {
        foreach ($this->addSlashArray as $key) {
            $libSearch[] = "/\\s" . $key . "/";
            $libSearch[] = "/\\(" . $key . "/";
            $libReplace[] = " \\$key";
            $libReplace[] = "(\\$key";
        }
        $files = $this->scanDirectory($path);
        echo "=====================\n";
        echo "Started ThirdParty Replacement \n";
        foreach ($files as $file) {
            if (file_exists($file)) {
                $contents = preg_replace($libSearch, $libReplace, file_get_contents($file));
                $contents=str_replace("\\\\Magento\\","\\Magento\\",$contents);
                file_put_contents($file, $contents);
            }
        }

    }

    private function gitRename($sourcePathModule, $targetPathModule)
    {

        $this->gitShell->execute(
            'git mv %s %s',
            array($sourcePathModule, $targetPathModule)
        );
        //$ git add app/code/Magento/<newModule>/
        $this->gitShell->execute(
            'git add %s',
            array($targetPathModule)
        );

    }

    private function getRelativePath($from, $to)
    {
        // some compatibility fixes for Windows paths
        $windows = false;
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to = is_dir($to) ? rtrim($to, '\/') . '/' : $to;
        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $windows = true;
        }

        $from = explode('/', $from);
        $to = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {
            // find first non-matching dir
            if ($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    $relPath[0] = './' . $relPath[0];
                }
            }
        }

        $path = implode('/', $relPath);
        if (!$windows) {
            $path = str_replace("\\", "/", $path);
        }

        return $path;
    }
}


//Processing CommandLine arguments
// php psrx.php (Dir/File)Location rootdirectory
// root directory


function errHandle($errNo, $errStr, $errFile, $errLine)
{
    $msg = "$errStr in $errFile on line $errLine";
    if ($errNo) {
        die($msg);
    }
}

set_error_handler('errHandle');

if (isset($argv[1])) {
    $rootDirectory = false;
    if (isset($argv[2])) {
        $rootDirectory = $argv[2];
        $update = explode("=", $argv[2]);
        if (isset($update[1])) {
            $rootDirectory = trim($update[1]);
        }
    }
    $src = explode("=", $argv[1]);
    if (isset($src[1])) {
        $src = trim($src[1]);

    } else {
        throw new exception("src paramter cannot be empty");
    }
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        if (is_dir('C:\Program Files (x86)\Git\cmd')) {
            exec('PATH=C:\Program Files (x86)\Git\cmd');
        } elseif (is_dir('C:\Program Files\Git\cmd')) {
            exec("PATH=C:\\Program Files\\Git\\cmd");
        }
        exec("git", $output, $ret);
        if (empty($output)) {
            die("Please set the git path Manually");
        }
    }

    $PSRX = new namespacer($src, $rootDirectory);
    $PSRX->convertToPSRX();
} else {
    echo "Please provide the arguments";
}

