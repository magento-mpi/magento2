<?php
/**
 *
 * Rename the Class packages under Tax module to TaxClass.
 */

require __DIR__ . '/../../../../../app/autoload.php';
Magento_Autoload_IncludePath::addIncludePath(__DIR__ . '/../../../../../lib');


class fixTaxClassReferences
{
    const FOUR_SPACES = '    ';
    private $path = null;
    private $namespace = array();
    private $splitLine = 0;
    private $reserveCheck = false;
    private $fileMapper = array();
    private $errorLog = "error.txt";
    private $fileChanged = array();
    private $rootDirectory = null;

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
            //echo "$file Class namespace fix started\n";
            $this->makeFile($file, $lines);
            //echo "$file tax class namespace transformation completed \n";


        }
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
        $stringBefore = $string;
        $string = str_replace('Magento_Tax_Model_Resource_Class' . '_', 'Magento_Tax_Model_Resource_TaxClass_', $string);
        $string = str_replace('Magento_Tax_Model_Class' . '_', 'Magento_Tax_Model_TaxClass_', $string);
        if ($stringBefore != $string) {
            echo "$file\n";
            file_put_contents($file, $string);
        }
        else {
            //echo "no change in $file.\n";
        }

    }
}

if (isset($argv[1])) {
    $src = explode("=", $argv[1]);
    if (isset($src[1])) {
        $src = trim($src[1]);

    } else {
        throw new exception("src parameter cannot be empty");
    }

    $shell = new Magento_Shell(null);
    $src = str_replace('\\','/', $src);
    $params = array(
        '../../../../../app/code/Magento/Tax/Model/Class',
        '../../../../../' . 'app/code/Magento/Tax/Model/TaxClass');
    if (realpath($params[0]) && !realpath($params[1])) {
        echo "git-moving $params[0] to $params[1]\n";
        $shell->execute(
            'git mv %s %s',
            $params);
    }
    else {
        echo "skipping already-moved $params[0]\n";
    }

    $params = array(
        '../../../../../app/code/Magento/Tax/Model/Resource/Class',
        '../../../../../' . 'app/code/Magento/Tax/Model/Resource/TaxClass');
    //git mv %s %s',
    if (realpath($params[0]) && !realpath($params[1])) {
        echo "git-moving $params[0] to $params[1]\n";
        $shell->execute(
            'git mv %s %s',
            $params);
    }
    else {
        echo "skipping already-moved $params[0]\n";
    }
    echo "Processing $src for references.\n";
    new fixTaxClassReferences($src);
} else {
    echo "Please provide the arguments";
}
