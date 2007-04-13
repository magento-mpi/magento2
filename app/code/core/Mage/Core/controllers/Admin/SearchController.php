<?php
class Mage_Core_SearchController extends Mage_Core_Controller_Admin_Action
{
    function doAction()
    {
        $data = '{
			totalCount : 6,
			topics : [
				{topic_title:"sdfsdfasdf", topic_id:"1", author:"begemot", post_text:"sfdasdfasdfasdf asdf asdfas dfas dasf dfasdf asdf"},
				{topic_title:"sdfsdfasdf", topic_id:"1", author:"begemot", post_text:"sfdasdfasdfasdf asdf asdfas dfas dasf dfasdf asdf"},
				{topic_title:"sdfsdfasdf", topic_id:"1", author:"begemot", post_text:"sfdasdfasdfasdf asdf asdfas dfas dasf dfasdf asdf"},
				{topic_title:"sdfsdfasdf", topic_id:"1", author:"begemot", post_text:"sfdasdfasdfasdf asdf asdfas dfas dasf dfasdf asdf"},
				{topic_title:"sdfsdfasdf", topic_id:"1", author:"begemot", post_text:"sfdasdfasdfasdf asdf asdfas dfas dasf dfasdf asdf"},
				{topic_title:"sdfsdfasdf", topic_id:"1", author:"begemot", post_text:"sfdasdfasdfasdf asdf asdfas dfas dasf dfasdf asdf"},
			]}';
        
        $this->getResponse()->setBody($data);
    }
}