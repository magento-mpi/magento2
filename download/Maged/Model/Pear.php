<?php

include_once "Maged/Pear.php";

class Maged_Model_Pear extends Maged_Model
{
    protected $_remotePackages;

    protected function _construct()
    {
        parent::_construct();
    }

    public function pear()
    {
        return Maged_Pear::getInstance();
    }
    
    public function validateWritable()
    {
        $ctrl = $this->controller();
        return is_writable($ctrl->getMageDir())
            && is_writable($ctrl->filepath())
            && is_writable($ctrl->filepath('config.ini'))
            && is_writable($ctrl->filepath('pearlib/pear.ini'))
            && is_writable($ctrl->filepath('pearlib/php'));
    }

    public function validateEnvironment()
    {
        $ok = true;

        if (!$this->validateWritable()) {
            $ok = false;
            $this->controller()->session()
                ->addMessage('error', 'Please make sure that all Magento files and folders are writable for the web server');
        }

        return $ok;
    }

    public function installAll($force=false)
    {
        $options = array('force'=>$force ? 1 : 0);
        $params = array('connect.magentocommerce.com/core/Metapkg_Core_Latest');
        $this->pear()->runHtmlConsole(array('command'=>'install', 'options'=>$options, 'params'=>$params));
    }

    public function upgradeAll()
    {
        $this->pear()->runHtmlConsole(array('command'=>'upgrade-all'));
    }

    public function getAllPackages()
    {
        $pear = $this->pear();

        $packages = array();

        $remote = array();
        foreach ($this->pear()->getMagentoChannels() as $channel) {
            $pear->getFrontend()->clear();
            $result = $pear->run('list-all', array('channel'=>$channel));
            $output = $pear->getOutput();
            if (empty($output)) {
                continue;
            }

            foreach ($output as $channelData) {
                $channelData = $channelData['output'];
                $channel = $channelData['channel'];
                if (!isset($channelData['headline'])) {
                    continue;
                }
                if (empty($channelData['data'])) {
                    continue;
                }
                foreach ($channelData['data'] as $category=>$pkglist) {
                    foreach ($pkglist as $pkg) {
                        $pkgNameArr = explode('/', $pkg[0]);
                        $pkgName = isset($pkgNameArr[1]) ? $pkgNameArr[1] : $pkgNameArr[0];
                        $packages[$channel][$pkgName] = array(
                            'category'=>$category,
                            'remote_version'=>isset($pkg[1]) ? $pkg[1] : '',
                            'local_version'=>isset($pkg[2]) ? $pkg[2] : '',
                            'summary'=>isset($pkg[3]) ? $pkg[3] : '',
                        );
                    }
                }
            }
        }

        foreach ($this->pear()->getMagentoChannels() as $channel) {
            $pear->run('list', array('channel'=>$channel));
            $output = $pear->getOutput();
            if (empty($output)) {
                continue;
            }
            foreach ($output as $channelData) {
                $channelData = $channelData['output'];
                $channel = $channelData['channel'];
                if (!is_array($channelData) || !isset($channelData['headline']) || !isset($channelData['data'])) {
                    continue;
                }
                foreach ($channelData['data'] as $pkg) {
                    if (!isset($packages[$channel][$pkg[0]])) {
                        $packages[$channel][$pkg[0]] = array(
                            'remote_version'=>'',
                            'category'=>'',
                            'summary'=>'',
                        );
                    }
                    $packages[$channel][$pkg[0]]['local_version'] = $pkg[1];
                    $packages[$channel][$pkg[0]]['state'] = $pkg[2];
                }
            }
        }

        //$testStatus = array('', 'installed-latest', 'upgrade-available', 'stand-alone');
        //$i=0;
        foreach ($packages as $channel=>&$pkgs) {
            foreach ($pkgs as $pkgName=>&$pkg) {
                $actions = array(''=>'');
                if (!$pkg['remote_version']) {
                    $status = 'stand-alone';
                    $actions['uninstall'] = 'Unistall';
                } elseif (!$pkg['local_version']) {
                    $status = 'install-available';
                    $actions['install'] = 'Install';
                } elseif ($pkg['local_version']==$pkg['remote_version']) {
                    $status = 'installed-latest';
                    $actions['reinstall'] = 'Reinstall';
                    $actions['uninstall'] = 'Uninstall';
                } elseif (version_compare($pkg['local_version'], $pkg['remote_version'])==-1) {
                    $status = 'upgrade-available';
                    $actions['upgrade'] = 'Upgrade';
                    $actions['uninstall'] = 'Uninstall';
                }
                //$status = $testStatus[$i++%count($testStatus)];
                $pkg['actions'] = $actions;
                $pkg['status'] = $status;
            }
        }

        return $packages;
    }

    public function applyPackagesActions($packages)
    {
        $actions = array();
        foreach ($packages as $package=>$action) {
            $actions[$action][] = str_replace('|', '/', $package);
        }
        foreach ($actions as $action=>$packages) {
            switch ($action) {
                case 'install': case 'uninstall': case 'upgrade':
                    $this->pear()->runHtmlConsole(array(
                        'command'=>$action,
                        'params'=>$packages
                    ));
                    break;

                case 'reinstall':
                    $this->pear()->runHtmlConsole(array(
                        'command'=>'install',
                        'options'=>array('force'=>1),
                        'params'=>$packages
                    ));
                    break;
            }
        }
    }

    public function installUriPackage($uri)
    {
        print_r($uri);
    }

    public function saveConfigPost($p)
    {
        $this->pear()->run('config-set', array(), array('preferred_state', $p['preferred_state']));
        return $this;
    }
}
