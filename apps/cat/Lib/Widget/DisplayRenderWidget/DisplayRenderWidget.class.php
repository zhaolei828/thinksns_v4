<?php

/**字段信息展示渲染部件
 * Class DisplayRenderWidget
 */
class DisplayRenderWidget extends Widget
{
    public function render($data)
    {
        $content ='<span class="bld">'. $data['field']['alias'] . '</span>：';
        //检测是否过期隐藏
        if ($data['overed'] && $data['field']['over_hidden']) {
            $content .= '<span class="cred" id="' . $data['data']['field']['name'] . '">过期隐藏</span>';
            return $content;
        }
        $content .= '<span  id="' . $data['data']['field']['name'] . '">';
        switch ($data['field']['input_type']) {
            case IT_SINGLE_TEXT: //单行文本
            case IT_MULTI_TEXT: //多行文本
                $content .= h($data['data']['data'][0]);
                break;
            case IT_EDITOR: //编辑器
                $content .="<br/>". h($data['data']['data'][0]);
                break;
            case IT_DATE: //日期
                $content .= date('Y-m-d', $data['data']['data'][0]);
                //dump($data['data']['data'][0]);exit;
                break;
            //选择框
            case IT_SELECT: //下拉框
                $content .= $data['data']['values']['data'][$data['data']['data'][0]];
                break;
            case IT_RADIO: //单选框
                $content .= $data['data']['values'][$data['data']['data'][0]];
                break;
            case IT_PIC:
                //单图片
                if(intval($data['data']['data'][0])==0)
                    return '';
                $content .= '<a target="_blank" href="'.getImageUrlByAttachId($data['data']['data'][0]).'"><img title="点击查看大图"  class="pic_size" src="' . getImageUrlByAttachId($data['data']['data'][0]) . '"></a>';
                break;
            case IT_CHECKBOX:
                $values = explode(',', $data['data']['data'][0]);
                foreach ($values as $v) {
                    $content .= $data['data']['values'][$v] . '&nbsp;&nbsp;';
                }
                break;


        }
        $content .= '</span>';
        return $content;
    }
}