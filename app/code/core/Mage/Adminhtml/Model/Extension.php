<?php
require_once 'Varien/Pear.php';

class Mage_Adminhtml_Model_Extension extends Varien_Object
{
    protected $_pear;

    public function generatePackageXml()
    {
        Mage::getSingleton('adminhtml/session')
            ->setLocalExtensionPackageFormData($this->getData());

        $pkg = new Varien_Pear_Package;
        $pfm = $pkg->getPfm();
        $pfm->setOptions(array(
            'packagedirectory'=>'.',
            'baseinstalldir'=>'.',
            'simpleoutput'=>true,
        ));

        $this->_setPackage($pfm);
        $this->_setRelease($pfm);
        $this->_setMaintainers($pfm);
        $this->_setDependencies($pfm);
        $this->_setContents($pfm);

        if (!$pfm->validate(PEAR_VALIDATE_NORMAL)) {
            echo "<pre>".print_r($this->getData(),1)."</pre>";
            echo "TEST:";
            echo "<pre>".print_r($pfm->getValidationWarnings(),1)."</pre>";
            return $this;
        }

        $this->setPackageXml($pfm->getDefaultGenerator()->toXml(PEAR_VALIDATE_NORMAL));
        return $this;
    }

    protected function _setPackage($pfm)
    {
        $pfm->setPackageType('php');
        $pfm->setChannel($this->getData('channel'));

        $pfm->setLicense($this->getData('license'), $this->getData('license_uri'));

        $pfm->setPackage($this->getData('name'));
        $pfm->setSummary($this->getData('summary'));
        $pfm->setDescription($this->getData('description'));
    }

    protected function _setRelease($pfm)
    {
        $pfm->addRelease();
        $pfm->setDate(date('Y-m-d'));

        $pfm->setAPIVersion($this->getData('api_version'));
        $pfm->setReleaseVersion($this->getData('release_version'));
        $pfm->setAPIStability($this->getData('api_stability'));
        $pfm->setReleaseStability($this->getData('release_stability'));
        $pfm->setNotes($this->getData('notes'));
    }

    protected function _setMaintainers($pfm)
    {
        $maintainers = $this->getData('maintainers');
        foreach ($maintainers['role'] as $i=>$role) {
            if (0===$i) {
                continue;
            }
            $handle = $maintainers['handle'][$i];
            $name = $maintainers['name'][$i];
            $email = $maintainers['email'][$i];
            $active = !empty($maintainers['active'][$i]) ? 'yes' : 'no';
            $pfm->addMaintainer($role, $handle, $name, $email, $active);
        }
    }

    protected function _setDependencies($pfm)
    {
        $pfm->clearDeps();
        $exclude = $this->getData('depends_php_exclude')!=='' ? explode(',', $this->getData('depends_php_exclude')) : false;
        $pfm->setPhpDep($this->getData('depends_php_min'), $this->getData('depends_php_max'), $exclude);
        $pfm->setPearinstallerDep('1.6.2');

        foreach ($this->getData('depends') as $deptype=>$deps) {
            foreach ($deps['type'] as $i=>$type) {
                if (0===$i) {
                    continue;
                }
                $name = $deps['name'][$i];
                $min = !empty($deps['min'][$i]) ? $deps['min'][$i] : false;
                $max = !empty($deps['max'][$i]) ? $deps['max'][$i] : false;
                $recommended = !empty($deps['recommended'][$i]) ? $deps['recommended'][$i] : false;
                $exclude = !empty($deps['exclude'][$i]) ? explode(',', $deps['exclude'][$i]) : false;
                if ($deptype!=='extension') {
                    $channel = preg_match('#^pear#i', $name) ? 'pear.php.net' : 'var-dev.varien.com';
                }
                switch ($deptype) {
                    case 'package':
                        if ($type==='conflicts') {
                            $pfm->addConflictingPackageDepWithChannel(
                                $name, $channel, false, $min, $max, $recommended, $exclude);
                        } else {
                            $pfm->addPackageDepWithChannel(
                                $type, $name, $channel, $min, $max, $recommended, $exclude);
                        }
                        break;

                    case 'subpackage':
                        if ($type==='conflicts') {
                            Mage::throwException(__("Subpackage can't be conflicting"));
                        }
                        $pfm->addSubpackageDepWithChannel(
                            $type, $name, $channel, $min, $max, $recommended, $exclude);
                        break;

                    case 'extension':
                        $pfm->addExtensionDep(
                            $type, $name, $min, $max, $recommended, $exclude);
                        break;
                }
            }
        }
    }

    protected function _setContents($pfm)
    {
        $pfm->addUsesrole('magecore', 'Varien_Pear_Role');

        $baseDir = $this->getRoleDir('mage').DS;

        $pfm->clearContents();
        $contents = $this->getData('contents');
        foreach ($contents['role'] as $i=>$role) {
            if (0===$i) {
                continue;
            }

            $roleDir = $this->getRoleDir($role).DS;
            $fullPath = $roleDir.$contents['path'][$i];

            switch ($contents['type'][$i]) {
                case 'file':
                    if (!is_file($fullPath)) {
                        Mage::throwException(__("Invalid file: %s", $fullPath));
                    }
                    $pfm->addFile('/', $contents['path'][$i], array('role'=>$role, 'md5sum'=>md5_file($fullPath)));
                    break;

                case 'dir':
                    if (!is_dir($fullPath)) {
                        Mage::throwException(__("Invalid directory: %s", $fullPath));
                    }
                    $path = $contents['path'][$i];
                    $ignore = $contents['ignore'][$i];
                    $this->_addDir($pfm, $role, $roleDir, $path, $ignore);
                    break;
            }
        }

    }

    protected function _addDir($pfm, $role, $roleDir, $path, $ignore)
    {
        $roleDirLen = strlen($roleDir);
        $entries = @glob($roleDir.$path.DS."*");
        if (!empty($entries)) {
            foreach ($entries as $entry) {
                $filePath = substr($entry, $roleDirLen);
                if (!empty($ignore) && preg_match($ignore, $filePath)) {
                    continue;
                }
                if (is_dir($entry)) {
                    $baseName = basename($entry);
                    if ('.'===$baseName || '..'===$baseName) {
                        continue;
                    }
                    $this->_addDir($pfm, $role, $roleDir, $filePath, $ignore);
                } elseif (is_file($entry)) {
                    $pfm->addFile('/', $filePath, array('role'=>$role, 'md5sum'=>md5_file($entry)));
                }
            }
        }
    }

    public function getRoles()
    {
        return PEAR_Command_Mage::getRoles();
    }

    public function getRoleDir($role)
    {
        $roles = $this->getRoles();
        return Varien_Pear::getInstance()->getConfig()->get($roles[$role]['dir_config']);
    }

    public function savePackage()
    {
        if (!$this->getPackageXml()) {
            $this->generatePackageXml();
        }

        $pear = Varien_Pear::getInstance();
        $dir = $pear->getConfig()->get('temp_dir');
        file_put_contents($dir.'/package.xml', $this->getPackageXml());

        $pkgver = $this->getName().'-'.$this->getReleaseVersion();
        $this->unsPackageXml();
        file_put_contents($dir.DS.$pkgver.'.ser', serialize($this->getData()));

        $result = $pear->run('mage-package', array('targetdir'=>$dir), array($dir.'/package.xml'));
        print_r($pear->getFrontend());
    }
}