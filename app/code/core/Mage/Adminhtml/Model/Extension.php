<?php
require_once 'Varien/Pear/Package.php';

class Mage_Adminhtml_Model_Extension extends Varien_Object
{
    protected $_roles;

    public function getPear()
    {
        return Varien_Pear::getInstance();
    }

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
            //echo "<pre>".print_r($this->getData(),1)."</pre>";
            //echo "TEST:";
            //echo "<pre>".print_r($pfm->getValidationWarnings(), 1)."</pre>";
            $message = $pfm->getValidationWarnings();
            //$message = $message[0]['message'];
             throw Mage::exception('Mage_Adminhtml', __($message[0]['message']));

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
        $baseDir = $this->getRoleDir('mage').DS;

        $pfm->clearContents();
        $contents = $this->getData('contents');
        $usesRoles = array();
        foreach ($contents['role'] as $i=>$role) {
            if (0===$i) {
                continue;
            }

            $usesRoles[$role] = 1;

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
                    $include = $contents['include'][$i];
                    $ignore = $contents['ignore'][$i];
                    $this->_addDir($pfm, $role, $roleDir, $path, $include, $ignore);
                    break;
            }
        }

        $pearRoles = $this->getRoles();
#echo "<pre>".print_r($usesRoles,1)."</pre>";
        foreach ($usesRoles as $role=>$dummy) {
            if (empty($pearRoles[$role]['package'])) {
                continue;
            }
            $pfm->addUsesrole($role, $pearRoles[$role]['package']);
        }
    }

    protected function _addDir($pfm, $role, $roleDir, $path, $include, $ignore)
    {
        $roleDirLen = strlen($roleDir);
        $entries = @glob($roleDir.$path.DS."*");
        if (!empty($entries)) {
            foreach ($entries as $entry) {
                $filePath = substr($entry, $roleDirLen);
                if (!empty($include) && !preg_match($include, $filePath)) {
                    continue;
                }
                if (!empty($ignore) && preg_match($ignore, $filePath)) {
                    continue;
                }
                if (is_dir($entry)) {
                    $baseName = basename($entry);
                    if ('.'===$baseName || '..'===$baseName) {
                        continue;
                    }
                    $this->_addDir($pfm, $role, $roleDir, $filePath, $include, $ignore);
                } elseif (is_file($entry)) {
                    $pfm->addFile('/', $filePath, array('role'=>$role, 'md5sum'=>md5_file($entry)));
                }
            }
        }
    }

    public function getRoles()
    {
        if (!$this->_roles) {
            $frontend = $this->getPear()->getFrontend();
            $config = $this->getPear()->getConfig();
            $pearMage = new PEAR_Command_Mage($frontend, $config);
            $this->_roles = $pearMage->getRoles();
        }
        return $this->_roles;
    }

    public function getRoleDir($role)
    {
        $roles = $this->getRoles();
        return Varien_Pear::getInstance()->getConfig()->get($roles[$role]['dir_config']);
    }

    public function getMaintainerRoles()
    {
        return array(
            'lead'=>'Lead',
            'developer'=>'Developer',
            'contributor'=>'Contributor',
            'helper'=>'Helper'
        );
    }

    public function savePackage()
    {
        if (!$this->getPackageXml()) {
            $this->generatePackageXml();
        }
        if (!$this->getPackageXml()) {
            return false;
        }

        $pear = Varien_Pear::getInstance();
        $dir = Mage::getBaseDir('var').DS.'pear';
        file_put_contents($dir.'/package.xml', $this->getPackageXml());

        $pkgver = $this->getName().'-'.$this->getReleaseVersion();
        $this->unsPackageXml();
        $this->unsRoles();
        file_put_contents($dir.DS.$this->getName().'.ser', serialize($this->getData()));

        return true;
    }

    public function createPackage()
    {
        $pear = Varien_Pear::getInstance();
        $dir = Mage::getBaseDir('var').DS.'pear';
        $result = $pear->run('mage-package', array('targetdir'=>$dir), array($dir.'/package.xml'));
        if ($result instanceof PEAR_Error) {
            return $result;
        }
        return true;
    }


    public function getStabilityOptions()
    {
        return array(
            'devel'=>'Development',
            'alpha'=>'Alpha',
            'beta'=>'Beta',
            'stable'=>'Stable',
        );
    }

    public function getKnownChannels()
    {
        /*
        $pear = Varien_Pear::getInstance();
        $pear->run('list-channels');
        $output = $pear->getOutput();
        $pear->getFrontend()->clear();

        $data = $output[0]['output']['data'];
        $arr = array();
        foreach ($data as $channel) {
            $arr[$channel[0]] = $channel[1].' ('.$channel[0].')';
        }
        */
        $arr = array(
            'connect.magentocommerce.com/core' => 'Magento Core Team',
            'connect.magentocommerce.com/community' => 'Magento Community',
            'var-dev.varien.com' => 'Varien Dev',
            'pear.php.net' => 'PEAR',
            'pear.phpunit.de' => 'PHPUnit',
        );
        return $arr;
    }

    public function loadLocal($package, $options=array())
    {
        $pear = $this->getPear();

        $pear->getFrontend()->clear();

        $result = $pear->run('info', $options, array($package));
        if ($result instanceof PEAR_Error) {
            Mage::throwException($result->message);
            break;
        }

        $output = $pear->getOutput();
        $pkg = new PEAR_PackageFile_v2;
        $pkg->fromArray($output[0]['output']['raw']);

        return $pkg;
    }

    public function loadRemote($package, $options=array())
    {
        $pear = $this->getPear();

        $pear->getFrontend()->clear();

        $result = $pear->run('remote-info', $options, array($package));
        if ($result instanceof PEAR_Error) {
            Mage::throwException($result->message);
            break;
        }

        $output = $pear->getOutput();
        $this->setData($output[0]['output']);

        return $this;
    }

    public function clearAllCache()
    {
        $cacheDir = Mage::getBaseDir('var').DS.'cache';
        mageDelTree($cacheDir);
        mkdir($cacheDir, 0777);
        return true;
    }
}
