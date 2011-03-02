<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @TODO
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Configuration file with constants.
 */
require_once 'configCreateTests.php';

/**
 * Class for automated creating PHPUnit tests.
 * Input data is array of path, file's name, class's name and method's name.
 * Result is list of classes, placed in need location.
 */
class Create
{
    /**
     * Contains array of data for creating testing classes and methods.
     * 
     * @var array 
     */
    private $_sourceData = array();

    /**
     * Template for class of PHPUnit test.
     *
     * @var string
     */
    private $_templateClass;

    /**
     * Template for method of PHPUnit test.
     *
     * @var string
     */
    private $_templateMethod;

    /**
     * Method for access to creating PHPUnit tests.
     */
    public function execute()
    {
        $this->setSourceData();
        $this->setTemplateTestingClass();
        $this->setTemplateTestingMethod();
        $this->createFiles();
    }

    private function setSourceData()
    {
        require_once PATH_TO_SOURCE_DATA;
        if(!empty($tests)){
            $this->_sourceData = $tests;
        }
    }

    private function setTemplateTestingClass()
    {        
        $this->_templateClass = file_get_contents(PATH_TO_TEMPLATE);
    }

    private function setTemplateTestingMethod ()
    {
         $this->_templateMethod = str_replace("<?php",'',file_get_contents(PATH_TO_TEMPLATE_METHOD));
    }

    private function createFiles()
    {
        if (count($this->_sourceData) > 0){
            foreach ($this->_sourceData as $data){
                $this->savePath($data);
            }
        }
        else throw new Exception ('Source data file is empty!');

    }

    private function savePath($data)
    {
        if (count($data) > 0){
            $dir = $this->makeDir($data[SOURCE_DATA_KEY_PATH]);
            $file = $this->makeFile($dir, $data[SOURCE_DATA_KEY_FILE]);
            $this->makeClass($file, $data[SOURCE_DATA_KEY_CLASS]);
            $this->makeMethod($file, $data[SOURCE_DATA_KEY_METHOD]);
        }
        else throw new Exception ('Incomplete data!');
    }

    private function makeDir ($path)
    {
        if (!is_dir(PATH_TO_TESTS_DIR . "/" . $path)){
            mkdir (PATH_TO_TESTS_DIR . "/" . $path, 0700, true);
        }        
        return $path;
    }

    private function makeFile ($pathToDir, $nameFile)
    {
        if (!file_exists($pathToDir . "/" . $nameFile)){
            $handle = fopen($pathToDir . "/" . $nameFile, 'w');
            if (is_resource($handle)){
                fwrite($handle, $this->_templateClass);
                fclose($handle);
            }
        }
        return $pathToDir . "/" . $nameFile;
    }

    private function makeClass ($file, $nameClass)
    {
        if (file_exists($file)){
            $fileData = file_get_contents($file);
            $fileData = str_replace(CLASSNAME_REPLACEMENT, $nameClass, $fileData);
            $handle = fopen($file, 'w');
            if (is_resource($handle)){
                fwrite($handle, $fileData);
                fclose($handle);
            }
        }
    }

    private function makeMethod ($file, $methodName)
    {
        $methodData = str_replace(METHODNAME_REPLACEMENT, $methodName, $this->_templateMethod);
        $fileClassData = file_get_contents($file);
        $fileClassData = preg_replace('/(\s}\s*)$/Ui', $methodData.'\1', $fileClassData);

        $handle = fopen($file, 'w');
        if (is_resource($handle)){
            fwrite($handle, $fileClassData);
            fclose($handle);
        }
    } 
    
}
