<?php

class Mage_Adminhtml_Model_Extension_Remote_Collection extends Mage_Adminhtml_Model_Extension_Collection_Abstract
{
    protected function _fetchPackages()
    {
        // fetch installed packages
        $pear = Varien_Pear::getInstance();

        $channels = Mage::getModel('adminhtml/extension')->getKnownChannels();
        #$channels = array('var-dev.varien.com'=>1);#, 'pear.php.net'=>1);
        $channelData = array();
        foreach ($channels as $channel=>$name) {
            if ($channel==='pecl.php.net') {
                continue;
            }
            $data = array();
            if (Mage::app()->useCache('pear')) {
                $channelKey = 'PEAR_channel_packages_'.preg_replace('#[^a-z0-9]+#', '_', $channel);
                $data = unserialize(Mage::app()->loadCache($channelKey));
            }
            if (empty($data)) {
                $pear->getFrontend()->clear();
                $pear->run('list-all', array('channel'=>$channel));
                $output = $pear->getOutput();
                if (empty($output)) {
                    continue;
                }
                $data = $output[0]['output'];
                if (Mage::app()->useCache('pear')) {
                    Mage::app()->saveCache(serialize($data), $channelKey, array('pear'), 3600);
                }
            }
            $channelData[$channel] = $data;
        }

        // load available packages into array
        $packages = array();
        foreach ($channelData as $channel) {
            if (!isset($channel['headline'])) {
                continue;
            }
            foreach ($channel['data'] as $category=>$pkglist) {
                foreach ($pkglist as $pkg) {
                    $packages[] = array(
                        'id'=>$channel['channel'].'|'.$pkg[0],
                        'category'=>$category,
                        'channel'=>$channel['channel'],
                        'name'=>$pkg[0],
                        'remote_version'=>isset($pkg[1]) ? $pkg[1] : '',
                        'local_version'=>isset($pkg[2]) ? $pkg[2] : '',
                        'summary'=>isset($pkg[3]) ? $pkg[3] : '',
                    );
                }
            }
        }

        return $packages;
    }
}