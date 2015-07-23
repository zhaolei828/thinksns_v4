<?php

class RecomLiTplWidget extends Widget
{
    /**
     * @param mixed $data
     * infos 全部信息
     * class 类名
     * type 类型
     * @return string
     */
    public function render($data)
    {
        if ($data['type'] == 'list') {
            if ($data['infos']['data'] == '') {
                return '';
            }

            $data['entity'] = D('cat_entity')->find($data['infos']['data'][0]['entity_id']);
            foreach ($data['infos']['data'] as $key => $vo) {
                $data['infos']['data'][$key]['user'] = D('User')->getUserInfo($vo['uid']);
            }
        } else {
            if ($data['infos'] == '') {
                return '';
            }

            $data['entity'] = D('cat_entity')->find($data['infos'][0]['entity_id']);
            foreach ($data['infos'] as $key => $vo) {
                $data['infos'][$key]['user'] = D('User')->getUserInfo($vo['uid']);
            }
        }

        $content = $this->renderFile(dirname(__FILE__) . '/tpl.html', $data);
        return $content;
    }
}