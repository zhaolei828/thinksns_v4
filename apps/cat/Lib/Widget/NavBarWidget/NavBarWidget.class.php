<?php

class NavBarWidget extends Widget
{
    public function render($data)
    {
        $map['show_nav'] = 1;
        $var['entitys'] = D('cat_entity')->where($map)->order('sort desc')->select();
        $content = $this->renderFile(dirname(__FILE__) . '/tpl.html', $var);
        return $content;
    }
}