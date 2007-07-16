<?php

class Mage_Core_Model_Mysql4_Config extends Mage_Core_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('core/config_data', 'config_id');
    }
    

    public function getChecksum()
    {
        $this->getConnection('checksum table '.$this->getMainTable().', '.$this->getTable('website').', '.$this->getTable('store'));
    }

    /**
     * Load configuration values into xml config object
     *
     * @param Mage_Core_Model_Config $xmlConfig
     * @param string $cond
     * @return Mage_Core_Model_Mysql4_Config_Collection
     */
    public function loadToXml(Mage_Core_Model_Config $xmlConfig, $cond=null)
    {
        $read = $this->getConnection('read');
        
        #$tables = $read->fetchAll("show tables like 'core_%'");
        #print_r($tables);
        
        $config = array();

        // load websites and stores from db
        $websites = $read->fetchAssoc("select website_id, code, name from ".$this->getTable('website'));
        $stores = $read->fetchAssoc("select store_id, code, name, website_id from ".$this->getTable('store'));
#print_r($websites);
        // initialize websites config
        foreach ($websites as $wId=>$wData) {
            $config['website'][$wId]['system/website/id']['value'] = $wId;
            $config['website'][$wId]['system/website/name']['value'] = $wData['name'];
        }
        
        //initialize stores config
        foreach ($stores as $sId=>$sData) {
            $wId = $sData['website_id'];
            $websites[$wId]['stores'][$sId] = 1;
            $config['website'][$wId]['system/stores/'.$stores[$sId]['code']]['value'] = $sId;
            $config['store'][$sId]['system/store/id']['value'] = $wId;
            $config['store'][$sId]['system/store/name']['value'] = $sData['name'];
            $config['store'][$sId]['system/website/id']['value'] = $sData['website_id'];
        }
        
        // load all configuration records from database
        $rows = $read->fetchAll("select * from ".$this->getMainTable().($cond ? " where ".$cond : ''));

        // get default distribution config vars
        $vars = Mage::getModel('core/config')->getDistroServerVars();
        foreach ($vars as $k=>$v) {
            $subst_from[] = '{{'.$k.'}}';
            $subst_to[] = $v;
        }
        // organize configuration records in $config array and associate stores to websites
        foreach ($rows as $r) {
            $r['data'] = str_replace($subst_from, $subst_to, $r['data']);
            $config[$r['scope']][$r['scope_id']][$r['path']] = array('value'=>$r['data'], 'inherit'=>$r['inherit']);
        }

        // inherit global -> website -> store configuration values
        foreach ($config['default'][0] as $path=>$data) {
            foreach ($config['website'] as $wId=>$wConfig) {
                if (!isset($wConfig[$path]) || !empty($wConfig[$path]['inherit'])) {
                    $config['website'][$wId][$path]['value'] = $data['value'];
                }
                foreach ($websites[$wId]['stores'] as $sId=>$dummy) {
                    $sConfig = $config['store'][$sId];
                    if (!isset($sConfig[$path]) || !empty($sConfig[$path]['inherit'])) {
                        $config['store'][$sId][$path]['value'] = $config['website'][$wId][$path]['value'];
                    }
                }
            }
        }
        
        // save into config object
        foreach ($config as $scope=>$scopeConfig) {
            foreach ($scopeConfig as $sId=>$sConfig) {
                foreach ($sConfig as $path=>$data) {
                    // get config prefix: 'global' or 'websites/{code}' or 'stores/{code}'
                    $prefix = $scope.($sId!==0 ? 's/'.${$scope.'s'}[$sId]['code'] : '');
                    $xmlConfig->setNode($prefix.'/'.$path, $data['value']);
                }
            }
        }
#echo "<xmp>".$xmlConfig->getNode()->asNiceXml()."</xmp>";
        return $this;
    }
}