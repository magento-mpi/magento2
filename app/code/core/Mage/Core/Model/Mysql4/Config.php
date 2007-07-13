<?php

class Mage_Core_Model_Mysql4_Config extends Mage_Core_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('core/config', 'config_id');
    }

    public function loadToXml(Mage_Core_Model_Config $xmlConfig, $cond=null)
    {
        $read = $this->getConnection('read');

        // load websites and stores from db
        $websites = $read->fetchAssoc("select website_id, code from ".$this->getTable('website'));
        $stores = $read->fetchAssoc("select store_id, code from ".$this->getTable('store'));

        // load all configuration records from database
        $rows = $read->fetchAll("select * from ".$this->getMainTable().($cond ? " where ".$cond : ''));

        // organize configuration records in $config array and associate stores to websites
        $config = array();
        foreach ($rows as $r) {
            $path = $r['config_section'].'/'.$r['config_group'].'/'.$r['config_field'];
            $config[$r['config_scope']][$r['config_scope_id']][$path] = array(
                'value'=>$r['config_data'],
                'inherit'=>$r['inherit']
            );
            if ($r['config_scope']==='store' && $path==='system/website/id') {
                $websites[$r['config_data']]['stores'][$r['config_scope_id']] = 1;
            }
        }

        // inherit global -> website -> store configuration values
        foreach ($config['global'][0] as $path=>$data) {
            foreach ($config['website'] as $wId=>$wConfig) {
                if (!isset($wConfig[$path]) || $wConfig[$path]['inherit']) {
                    $config['website'][$wId][$path]['value'] = $data['value'];
                }
                foreach ($websites[$wId]['stores'] as $sId=>$dummy) {
                    $sConfig = $config['store'][$sId];
                    if (!isset($sConfig[$path]) || $sConfig[$path]['inherit']) {
                        $config['store'][$sId][$path]['value'] = $config['website'][$wId][$path]['value'];
                    }
                }
            }
        }

        // save into config object
        foreach ($config['store'] as $sId=>$sConfig) {
            foreach ($sConfig as $path=>$data) {
                $nodePath = 'stores/'.$stores[$sId]['code'].'/'.$path;
                $xmlConfig->setNode($nodePath, $data['value']);
            }
        }

        return $this;
    }
}