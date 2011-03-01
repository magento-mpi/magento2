<?php
ini_set('include_path', ini_get('include_path').PATH_SEPARATOR.dirname(__FILE__).'/../');
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/Framework/TestSuite.php';

require_once 'Mage.php';

class UnitTestSuite extends WebService_Utils_TestSuite_Abstract
{
    protected $_suite = "Unit";
    
    public function __construct($theClass = '', $name = '')
    {
        parent::__construct($theClass, $name);

        $this->_dirClassPath = dirname(__FILE__);
        $this->_configFilePath = $this->_dirClassPath.'/etc/config.xml';
        
        $this->_initSuite();
    }


    protected function setUp()
    {
        Mage::app('admin');
        
        parent::setUp();
    }

    protected function tearDown()
    {
        parent::tearDown();
    }

    public static function suite()
    {
        return new self();
    }
}