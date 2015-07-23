<?php

class LiNavWidget extends Widget
{
    /**
     * @param mixed $data
     * name entity.name
     * title li内容
     * @return string
     */
    public function render($data)
    {
        if (ACTION_NAME == 'li' and $_GET['name'] == $data['name']) {
            $content = '<a href="' . U('cat/Index/li', array('name' => $data['name'])) . '"class="current"><div>' . $data['title'] .'</div></a>';
        }
        else{
            $content = '<a href="' . U('cat/Index/li', array('name' => $data['name'])) . '"><div>' . $data['title'] .'</div></a>';
        }
        return $content;


    }
}