<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 95
 * Date: 13-7-7
 * Time: 下午7:30
 * To change this template use File | Settings | File Templates.
 */

class SearchInputRenderWidget extends Widget
{
    public function render($data)
    {
        $field = $data['field'];
        switch ($field['input_type']) {
            case IT_SINGLE_TEXT:
            case IT_MULTI_TEXT;
            case IT_EDITOR:
                $file = '/text.html';
                break;
            case IT_SELECT:

                $file = '/select.html';
                break;
            case IT_RADIO:
                $file = '/radio.html';
                break;
            case IT_CHECKBOX:
                $file = '/checkbox.html';
                break;
            default:
                $file = '/text.html';
        }
        $content = $this->renderFile(dirname(__FILE__) . $file, $data);
        return $content;
    }
}