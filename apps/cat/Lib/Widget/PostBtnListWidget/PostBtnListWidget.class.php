<?php

class PostBtnListWidget extends Widget
{
    public function render($data)
    {
        $map['show_post'] = 1;
        $entitys = D('cat_entity')->where($map)->order('sort desc')->select();
        foreach ($entitys as $k => $v) {
            if (isset($data['name'])) {
                if (!CheckCanPostEntityN($this->mid, $v['name'])) {
                    $entitys[$k]['hidden']=1;
                }
            }
            if (isset($data['entity_id'])) {
                if (!CheckCanReadEntity($this->mid, $v['entity_id'])) {
                    $entitys[$k]['hidden']=1;
                }
            }

        }

        $var['entitys']=$entitys;
        $var['mid'] = $this->mid;
        $content = $this->renderFile(dirname(__FILE__) . '/tpl.html', $var);
        return $content;
    }
}