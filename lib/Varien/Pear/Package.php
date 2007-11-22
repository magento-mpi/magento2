<?php

require_once "Varien_Pear.php";

require_once "PEAR/PackageFileManager2.php";
// add missing but required constant...
define ('PEAR_PACKAGEFILEMANAGER_NOSVNENTRIES', 1001);
$GLOBALS['_PEAR_PACKAGEFILEMANAGER2_ERRORS']['en']['PEAR_PACKAGEFILEMANAGER_NOSVNENTRIES'] =
    'Directory "%s" is not a SVN directory (it must have the .svn/entries file)';

class Varien_Pear_Package
{
    protected $_data = array(
        'options' => array(),
        'package' => array(),
        'release' => array(),
    );

    protected $_pfm;

    static public function run()
    {
        $pkg = new self;
        $pkg->generatePackage();
    }

    public function getPear()
    {
        return Varien_Pear::getInstance();
    }

    public function getPearConfig($key)
    {
        return $this->getPear()->getConfig()->get($key);
    }

    public function set($key, $data)
    {
        if (''===$key) {
            $this->_data = $data;
            return $this;
        }

        // accept a/b/c as ['a']['b']['c']
        $keyArr = explode('/', $key);
        $ref = &$this->_data;
        for ($i=0, $l=sizeof($keyArr); $k=$keyArr[$i], $i<$l; $i++) {
            if (!isset($ref[$k])) {
                $ref[$k] = array();
            }
            $ref = &$ref[$k];
        }
        $ref = $data;

        return $this;
    }

    public function get($key)
    {
        if (''===$key) {
            return $this->_data;
        }

        // accept a/b/c as ['a']['b']['c']
        $keyArr = explode('/', $key);
        $data = $this->_data;
        foreach ($keyArr as $i=>$k) {
            if ($k==='') {
                return null;
            }
            if (is_array($data)) {
                if (!isset($data[$k])) {
                    return null;
                }
                $data = $data[$k];
            } else {
                return null;
            }
        }
        return $data;
    }

    public function setPfm($pfm)
    {
        $this->_pfm = $pfm;
        return $this;
    }

    public function getPfm()
    {
        if (!$this->_pfm) {
            $this->_pfm = PEAR_PackageFileManager2::importOptions('package2.xml', $this->get('options'));
        }
        return $this->_pfm;
    }

    public function generatePackage($make=false)
    {
        PEAR::setErrorHandling(PEAR_ERROR_DIE);

        $this->defineData();

        $this->definePackage();

        $pfm = $this->getPfm();
        $pfm->addRelease();

        $this->defineRelease();

        $pfm->generateContents();
        $pfm1 = $pfm->exportCompatiblePackageFile1($this->get('options'));

        if ($make) {
            $pfm1->writePackageFile();
            $pfm->writePackageFile();

            $outputDir = $this->get('options/outputdirectory');
            MagePearWrapper::getInstance()->run('package', array(),
                array($outputDir.'package.xml', $outputDir.'package2.xml')
            );
        } else {
            $pfm1->debugPackageFile();
            $pfm->debugPackageFile();
        }

        return $this;
    }

    public function defineData()
    {
        $this->set('options/outputdirectory', $this->getPear()->getPearDir().DS.'output');
        $this->set('options/filelistgenerator', 'php');
        $this->set('options/simpleoutput', true);

        return $this;
    }

    public function definePackage()
    {
        return $this;
    }

    public function defineRelease()
    {
        return $this;
    }
}