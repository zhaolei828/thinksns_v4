<?php

class UEditorWidget extends Widget
{
    public function render($data)
    {
        $data['content'] = isset($data['content']) ? $data['content'] : '';
        $data['name'] = isset($data['name']) ? $data['name'] : 'content';
        $data['id'] = isset($data['id']) ? $data['id'] : 'id';
        $data['toolbars'] = isset($data['toolbars']) ? $data['toolbars'] : "['bold', 'justifyleft', 'justifycenter', 'justifyright', 'insertimage', 'insertvideo', 'emotion','fullscreen']";
        $data['z_index'] = isset($data['z_index']) ? $data['z_index'] : 10;
        $data['width'] = isset($data['width']) ? $data['width'] : 650;
        $data['height'] = isset($data['height']) ? $data['height'] : 150;
        $content = $this->renderFile(dirname(__FILE__) . '/ueditor.html', $data);
        return $content;
    }
}