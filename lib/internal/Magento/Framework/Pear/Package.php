<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/*
require_once "Magento/Framework/Pear.php";

require_once "PEAR/PackageFileManager.php";
require_once "PEAR/PackageFile/v1.php";
require_once "PEAR/PackageFile/Generator/v1.php";

require_once "PEAR/PackageFileManager2.php";
require_once "PEAR/PackageFile/v2.php";
require_once "PEAR/PackageFile/v2/rw.php";
require_once "PEAR/PackageFile/v2/Validator.php";
namespace Magento\Framework\Pear;


// add missing but required constant...
define ('PEAR_PACKAGEFILEMANAGER_NOSVNENTRIES', 1001);
$GLOBALS['_PEAR_PACKAGEFILEMANAGER2_ERRORS']['en']['PEAR_PACKAGEFILEMANAGER_NOSVNENTRIES'] =
   'Directory "%s" is not a SVN directory (it must have the .svn/entries file)';

require_once "PEAR/PackageFile/Generator/v2.php";
*/

namespace Magento\Framework\Pear;

use Magento\Framework\Pear;

class Package
{
    /**
     * @var array
     */
    protected $_data = [
        'options' => [
            'baseinstalldir' => '',
            'filelistgenerator' => 'file',
            'packagedirectory' => '.',
            'outputdirectory' => '.',
        ],
        'package' => [],
        'release' => [],
    ];

    /**
     * @var Pear
     */
    protected $_pear;

    /**
     * @var PEAR_PackageFileManager2
     */
    protected $_pfm;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_pear = Pear::getInstance();
    }

    /**
     * @return Pear
     */
    public function getPear()
    {
        return $this->_pear;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getPearConfig($key)
    {
        return $this->getPear()->getConfig()->get($key);
    }

    /**
     * @param string $key
     * @param array $data
     * @return $this
     */
    public function set($key, $data)
    {
        if ('' === $key) {
            $this->_data = $data;
            return $this;
        }

        // accept a/b/c as ['a']['b']['c']
        $keyArr = explode('/', $key);

        $ref = & $this->_data;
        for ($i = 0, $l = sizeof($keyArr); $i < $l; $i++) {
            $k = $keyArr[$i];
            if (!isset($ref[$k])) {
                $ref[$k] = [];
            }
            $ref = & $ref[$k];
        }
        $ref = $data;

        return $this;
    }

    /**
     * @param string $key
     * @return array|null
     */
    public function get($key)
    {
        if ('' === $key) {
            return $this->_data;
        }

        // accept a/b/c as ['a']['b']['c']
        $keyArr = explode('/', $key);
        $data = $this->_data;
        foreach ($keyArr as $i => $k) {
            if ($k === '') {
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

    /**
     * @param PEAR_PackageFileManager2 $pfm
     * @return $this
     */
    public function setPfm($pfm)
    {
        $this->_pfm = $pfm;
        return $this;
    }

    /**
     * Get PackageFileManager2 instance
     *
     * @param string|null $package
     * @return PEAR_PackageFileManager2
     * @throws PEAR_Exception
     */
    public function getPfm($package = null)
    {
        if (!$this->_pfm) {
            if (is_null($package)) {
                $this->_pfm = new PEAR_PackageFileManager2();
                $this->_pfm->setOptions($this->get('options'));
            } else {
                $this->defineData();

                $this->_pfm = PEAR_PackageFileManager2::importOptions($package, $this->get('options'));
                if ($this->_pfm instanceof PEAR_Error) {
                    $e = PEAR_Exception('Could not instantiate PEAR_PackageFileManager2');
                    $e->errorObject = $this->_pfm;
                    throw $e;
                }
            }
        }
        return $this->_pfm;
    }

    /**
     * @return $this
     */
    public function clearPackage()
    {
        $pfm = $this->getPfm();
        $pfm->clearContents();
        $pfm->clearCompatible();
        $pfm->clearDeps();
        $pfm->clearChangeLog();
        return $this;
    }

    /**
     * @param bool $make
     * @return $this
     */
    public function generatePackage($make = false)
    {
        PEAR::setErrorHandling(PEAR_ERROR_DIE);

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
            MagePearWrapper::getInstance()->run(
                'package',
                [],
                [$outputDir . 'package.xml', $outputDir . 'package2.xml']
            );
        } else {
            $pfm1->debugPackageFile();
            $pfm->debugPackageFile();
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function defineData()
    {
        $this->set('options/outputdirectory', $this->getPear()->getPearDir() . '/output');
        $this->set('options/filelistgenerator', 'php');
        $this->set('options/simpleoutput', true);

        return $this;
    }

    /**
     * @return $this
     */
    public function definePackage()
    {
        return $this;
    }

    /**
     * @return $this
     */
    public function defineRelease()
    {
        return $this;
    }
}
