<?php

class DefaultInfoTplWidget extends Widget
{
    public function render($data)
    {

        $data['entity'] = D('cat_entity')->find($data['info']['entity_id']);
        $data['data'] = D('Data')->getByInfoId($data['info']['info_id']);
        $data['user'] = D('User')->getUserInfo($data['info']['uid']);
        $map['info_id'] = $data['info']['info_id'];
        $data['info']['com'] = D('Com')->getList($map, 5);
        $data['mid']=$this->mid;
        $content = $this->renderFile(dirname(__FILE__) . '/tpl.html', $data);
        return $content;
    }
}