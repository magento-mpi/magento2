<?php

class Mage_Adminhtml_Model_Mysql4_Config extends Mage_Core_Model_Mysql4_Abstract 
{
	protected function _construct()
	{
		$this->_init('core/config_data', 'config_id');
	}
	
    public function loadSectionData($section, $website, $store)
    {
        $read = $this->getConnection('read');
        $table = $this->getMainTable();

        $config = array();

        $defaultConfig = $read->fetchAssoc(
            $read->select()->from($table, array('path', 'value', 'old_value'))
                ->where("scope='default'")
                ->where("path like ?", $section.'/%')
        );
        foreach ($defaultConfig as $path=>$data) {
            $config[$path] = array(
                'value'=>$data['value'],
                'default_value'=>'',
                'old_value'=>'',
            );
            if (!$website && !$store) {
                $config[$path]['old_value'] = $data['old_value'];
                $config[$path]['inherit']  = 0;
            } else {
                $config[$path]['default_value'] = $data['value'];
                $config[$path]['inherit']  = 1;
            }
        }

        if ($website) {
            $websiteId = (int)Mage::getConfig()->getNode("websites/$website/system/website/id");
            $websiteConfig = $read->fetchAssoc(
                $read->select()->from($table, array('path', 'value', 'inherit', 'old_value'))
                    ->where("scope='websites' and scope_id=?", $websiteId)
                    ->where("path like ?", $section.'/%')
            );
            foreach ($websiteConfig as $path=>$data) {
                $config[$path]['value'] = $data['value'];
                $config[$path]['inherit'] = $data['inherit'];
                if ($store) {
                    $config[$path]['default_value'] = $data['value'];
                } else {
                    $config[$path]['old_value'] = $data['old_value'];
                }
            }
        }

        if ($store) {
            $storeId = (int)Mage::getConfig()->getNode("stores/$store/system/store/id");
            $storeConfig = $read->fetchAssoc(
                $read->select()->from($table, array('path', 'value', 'inherit', 'old_value'))
                    ->where("scope='stores' and scope_id=?", $storeId)
                    ->where("path like ?", $section.'/%')
            );
            foreach ($storeConfig as $path=>$data) {
                $config[$path]['value'] = $data['value'];
                $config[$path]['old_value'] = $data['old_value'];
                $config[$path]['inherit'] = $data['inherit'];
            }
        }

        return $config;
    }

    public function saveSectionPost($section, $website, $store, $groups)
    {
        if (empty($groups)) {
            return $this;
        }

        if ($store) {
            $scope = 'stores';
            $scopeId = Mage::getStoreConfig('system/store/id', $store);
        } elseif ($website) {
            $scope = 'websites';
            $scopeId = Mage::getModel('core/website')->setCode($website)->getConfig('system/website/id');
        } else {
            $scope = 'default';
            $scopeId = 0;
        }

        $select = $this->getConnection('read')->select()
            ->from($this->getMainTable(), array('path', 'value', 'config_id', 'inherit'))
            ->where('scope=?', $scope)->where('scope_id=?', $scopeId)
            ->where('path like ?', $section.'/%');

        $old = $this->getConnection('read')->fetchAssoc($select);

        $dataModel = Mage::getModel('core/config_data');
        $rows = array();
        foreach ($groups as $group=>$groupData) {
            foreach ($groupData['fields'] as $field=>$fieldData) {
                if (!isset($fieldData['value'])) {
                    $fieldData['value'] = null;
                }
                if (is_array($fieldData['value'])) {
                    $fieldData['value'] = join(',', $fieldData['value']);
                }
                $path = $section.'/'.$group.'/'.$field;
                if (isset($fieldData['inherit'])) {
                    switch ($fieldData['inherit']) {
                        case 0:
                            break;
                        case 1:
                            #$fieldData['value'] = $fieldData['default_value'];
                            break;
                        case -1:
                            $fieldData['value'] = $fieldData['old_value'];
                            $fieldData['inherit'] = 0;
                            break;
                    }
                } else {
                    $fieldData['inherit'] = 0;
                }
                if (!isset($old[$path])
                    || $fieldData['value']!=$old[$path]['value']
                    || isset($fieldData['inherit']) && $fieldData['inherit']!=$old[$path]['inherit']) {

                    if (isset($old[$path]) && $fieldData['value']!=$old[$path]['value']) {
                        $fieldData['old_value'] = $old[$path]['value'];
                    } else {
                        $fieldData['old_value'] = '';
                    }

                    $data = array(
                        'config_id' => isset($old[$path]) ? $old[$path]['config_id'] : null,
                        'scope'     => $scope,
                        'scope_id'  => $scopeId,
                        'path'      => $path,
                        'old_value' => $fieldData['old_value'],
                        'inherit'   => $fieldData['inherit'],
                    );
                    if (!is_null($fieldData['value'])) {
                        $data['value'] = $fieldData['value'];
                    }
                    if (!isset($data['value']) || is_null($data['value']) || $data['value']==='') {
                    	continue;
                    }
                    $dataModel->setData($data)->save();
                }
            }
        }
        return $this;
    }
}