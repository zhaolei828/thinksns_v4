<?php
/**字段输入渲染部件
 * Class InputRenderWidget
 */
class InputRenderWidget extends Widget
{
    public function render($data)
    {
        $content = '<div class="cat_row1">';

        if ($data['field']['can_empty'] == 0) {
            $content .= '<span class="c_empty">*</span>';
        }
        $content .= '<label for="' . $data['field']['name'] . '">' . $data['field']['alias'];


        $content .= '</label></div><div class="cat_row2"> ';
        $values = json_decode($data['field']['option'], true);
        $data['field']['values'] = $values;
        if (isset($data['info_id'])) {
            if ($data['field']['name'] == 'over_time') {
                $info = D('cat_info')->find($data['info_id']);
                $data['field_value'] = $info['over_time'];
            } else {
                $map_filed_val['info_id'] = $data['info_id'];
                $map_filed_val['field_id'] = $data['field']['field_id'];
                $field_vals = D('cat_data')->where($map_filed_val)->limit(1)->select();
                $data['field_value'] = $field_vals[0]['value'];
            }

            //dump($data);
            //exit;
        }
        switch ($data['field']['input_type']) {
            case IT_SINGLE_TEXT:
                $content .= $this->renderFile(dirname(__FILE__) . '/single_text.html', $data);
                break;
            case IT_MULTI_TEXT:
                $content .= $this->renderFile(dirname(__FILE__) . '/multi_text.html', $data);
                break;
            case IT_SELECT:
                $content .= $this->renderFile(dirname(__FILE__) . '/select.html', $data);
                break;
            case IT_DATE:
                $content .= $this->renderFile(dirname(__FILE__) . '/date.html', $data);
                break;
            case IT_EDITOR:
                $content .= $this->renderFile(dirname(__FILE__) . '/editor.html', $data);
                break;
            case IT_RADIO:
                $content .= $this->renderFile(dirname(__FILE__) . '/radio.html', $data);
                break;
            case IT_PIC:
                $content .= $this->renderFile(dirname(__FILE__) . '/pic.html', $data);
                break;

            case IT_CHECKBOX:

                $content .= $this->renderFile(dirname(__FILE__) . '/checkbox.html', $data);
                breal;
        }
        $content .= '<div class="cat_tip mb10"><ul>';
        if ($data['field']['tip'] != '') {
            $content .= "<li>*" . $data['field']['tip'] . '</li>';
        }


        if ($data['field']['over_hidden']) {
            $content .= '<li class="cat_over_hidden">*该内容过期自动隐藏</li>';
        }
        $content .= "</ul></div></div>";
        $content .= '<div class="cat_row3">&nbsp;</div>';
        return $content;
    }
}