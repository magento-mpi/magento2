<?php

class Mage_Core_Model_Mysql4_Block extends Mage_Core_Model_Mysql4
{    
    function loadGroup($groupName)
    {
        $groupTable = $this->_getTableName('core_read', 'block_group');
        $blockTable = $this->_getTableName('core_read', 'block');
        
        $group = $this->_read->fetchRow("select *
            from $groupTable where group_name=?", array($groupName));
        
        if (empty($group)) {
            return false;
        }
        
        $blocks = $this->_read->fetchAll("select *
            from $blockTable where group_id=?", array($group['group_id']));
        
        $loaded = array();
        foreach ($blocks as $row) {
            $block = Mage::createBlock($row['block_type'], $row['block_name']);
            $block->setInfo(array('groupId'=>$row['group_id'], 'groupName'=>$group['group_name']));
            $loaded[] = array('block'=>$block, 'data'=>$row['data_serialized']);
        }
        
        // load attributes and children after all blocks of the group has been created
        if (!empty($loaded)) {
            foreach ($loaded as $row) {
                $data = unserialize($row['data']);
                if (!empty($data)) {
                    $row['block']->loadFromArray($data);
                }
            }
        }
        
        return true;
    }

    function saveGroup($groupName)
    {
        $groupTable = $this->_getTableName('core_write', 'block_group');
        $blockTable = $this->_getTableName('core_write', 'block');
        
        $group = $this->_read->fetchRow("select * from $groupTable where group_name=?", array($groupName));

        if (empty($group)) {
            $group = array('group_name'=>$groupName);
            $this->_write->insert($groupTable, $group);
            $group['group_id'] = $this->_write->lastInsertId();
        }
        
        $oldBlocks = $this->_read->fetchPairs("select block_id, block_name 
            from $blockTable where group_id=?", array($group['group_id']));
        
        $newBlocks = Mage_Core_Block::getBlocksByGroup($groupName);
        
        $delete = array();
        foreach ($oldBlocks as $id=>$name) {
            if (empty($newBlocks[$name])) {
                $delete[] = $id;
            }
        }
        
        if (!empty($delete)) {
            $this->_write->delete($blocktable, $this->_write->quoteInto('block_id in (?)', $delete));
        }
        
        foreach ($newBlocks as $name=>$block) {
            $block->setInfo(array('groupId'=>$group['group_id'], 'groupName'=>$groupName));
            $this->saveBlock($block);
        }
        
        return true;
    }
    
    function saveBlock($block)
    {
        $groupTable = $this->_getTableName('core_write', 'block_group');
        $blockTable = $this->_getTableName('core_write', 'block');

        if (is_string($block)) {
            $block = Mage::getBlock($block);
            if (!$block) {
                Mage::exception('Non-existing block: '.$block);
            }
        }
        
        if (!$block instanceof Mage_Core_Block_Abstract) {
            Mage::exception('Invalid block object received in saveBlock');
        }
        
        $info = $block->getInfo();
        
        if (empty($info['groupName'])) {
            Mage::exception('Can not save a block without group name set: '.$info['name']);
        }
        
        $row['group_id'] = $info['groupId'];
        $row['block_type'] = $info['type'];
        $row['block_name'] = $info['name'];
        $row['data_serialized'] = serialize($block->toArray());
        
        if ($block->getInfo('saveParent')) {
            $row['parent_name'] = $block->getInfo('parent')->getInfo('name');
        }

        if (empty($info['id'])) {
            if (empty($info['groupId'])) {
                $info['groupId'] = $this->_read->fetchOne("select group_id
                    from $groupTable where group_name=?", array($info['groupName']));
            }
            $info['id'] = $this->_read->fetchOne("select block_id 
                from $blockTable where group_id=? and block_name=?", 
                array($info['groupId'], $info['name']));
        }
        
        if (!empty($info['id'])) {
            $this->_write->update($blockTable, $row, $this->_write->quoteInto("block_id=?", array('id'=>$info['id'])));       
        } else {
            $this->_write->insert($blockTable, $row);
            $block->setInfo('id', $this->_write->lastInsertId());
        }
        
        return true;
    }
    
}