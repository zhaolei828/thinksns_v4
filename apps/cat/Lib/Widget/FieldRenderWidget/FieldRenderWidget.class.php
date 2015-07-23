<?php

class FieldRenderWidget extends Widget
{
    public function render($data)
    {

        $rs = $data['str'];
        $field = $data['field'];
        $value = '';

        switch ($field['field']['input_type']) {
            case IT_SINGLE_TEXT: //单行文本
            case IT_MULTI_TEXT:
                $value = t($field['data'][0]);
                break;
            case IT_EDITOR:
                $value = h($field['data'][0]);
                break;
            //选择框

            case IT_SELECT:
                $value = t($field['values']['data'][$field['data'][0]]);
                break;
            case IT_PIC:
                $value = getImageUrlByAttachId($field['data'][0]);
                break;
            case IT_RADIO:
                $value = t($field['values'][$field['data'][0]]);
                break;
            case IT_CHECKBOX:

                $values = explode(',', $field['data'][0]);

                foreach ($values as $v) {
                    $value .= $field['values'][$v] . '&nbsp;&nbsp;';
                }
                break;
                //$value =  t($field['values']['data'][$field['data'][0]]);
                break;

        }

        if($data['only_value'])//如果只是显示值，用于html文件形式渲染
        {
            return $value;
        }

        $rs = str_replace('{$' . $field['field']['name'] . '}', $value, $rs);

        return $rs;
    }
}