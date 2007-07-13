<?php

class Mage_Core_Model_Mysql4_Config extends Mage_Core_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('core/config', 'config_id');
    }

    public function exportToXml(Mage_Code_Model_Config_Element $xml, $cond=null)
    {
        $sql = "select * from ".$this->getMainTable();
        if ($cond) {
            $sql .= " where ".$cond;
        }
        $rows = $this->getConnection('read')->fetchAll($sql);
        
        $config = array(); 
        $websites = array();
        foreach ($rows as $r) {
            $path = $r['config_section'].'/'.$r['config_group'].'/'.$r['config_field'];
            $config[$r['config_scope']][$r['config_scope_id']][$path] = array(
                'value'=>$r['config_data'],
                'inherit'=>$r['inherit']
            );
            if ($r['config_scope']==='store' && $path==='system/website/id') {
                $websites[$r['config_data']][$r['config_scope_id']] = 1;
            }
        }
        
        foreach ($config['global'][0] as $path=>$data) {
            foreach ($config['website'] as $wId=>&$wConfig) {
                if (!isset($wConfig[$path]) || $wConfig[$path]['inherit']) {
                    $wConfig[$path]['value'] = $data['value'];
                }
            }
        }
        
        foreach ($config['website'] as $wId=>$wConfig) {
            
            
        }
    }
}