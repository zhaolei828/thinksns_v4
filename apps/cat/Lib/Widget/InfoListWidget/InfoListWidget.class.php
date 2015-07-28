<?php

/**信息列表部件
 * Class InfoListWidget
 */
class InfoListWidget extends Widget
{
    private $_class = 'cat_ul_list'; //设置的类
    private $_type = '';

    /**
     * @param mixed $data
     * tpl 整数 模板
     * type list or limit 模板构造型
     * num 显示的记录条数
     * order 排序
     * entity_id 实体的id，用于查询
     * name 实体的名，用于查询，优先级低于entity_id
     * @return string
     */
    public function render($data)
    {
        define('NORES','<div style="padding:20px;color: #999">暂无内容</div>');
        /*初始化所有的参数*/
        // 从$data中读取参数
        $this->_type = isset($data['type']) ? $data['type'] : 'limit';
        $this->_class = isset($data['class']) ? $data['class'] : 'cat_ul_list';
        $tpl = isset ($data['tpl']) ? $data['tpl'] : -1;
        $num = isset($data['num']) ? $data['num'] : 10;
        $order = isset($data['order']) ? $data['order'] : 'cTime desc';
        $recom = isset($data['recom']) ? $data['recom'] : false;
        // 置顶排序
        $order = 'top desc,' . $order;

        $map = array();
        if($recom) {
            $map['recom'] = 1;
        }

        /*初始化所有的参数end*/

        /*获取到查询的条件*/
        if (isset($data['entity_id'])) {
            //通过entity_id查到entity
            $map['entity_id'] = $data['entity_id'];
            //获取预置的模板
            $entity = D('cat_entity')->find($data['entity_id']);
        } else {
            //通过name查到entity,和entity的id
            $map_t['name'] = $data['name'];
            $entitys = D('cat_entity')->where($map_t)->limit(1)->select();
            $entity = $entitys[0];
            $map['entity_id'] = $entity['entity_id'];
        }
        /*获取到查询的条件end*/
        if ($entity['need_active']) {
            $map['active'] = 1;
        }


        $info_ids = null; //初始的情况下是不做限定的
        if (isset($data['map']) && count($data['map'])) {
            /*清除干扰条件*/
            $li = $this->unsetOtherParm($data);
            /*清除干扰条件end*/

            foreach ($li as $key => $data_value) {
                if ($data_value == '' || $key=='p') {
                    //如果这个参数是没有值的，就略过
                    continue;
                }
                /*查出field的field_id*/
                $s_m['name'] = $key;
                $s_m['entity_id'] = $entity['entity_id'];
                $search_fields = D('cat_field')->where($s_m)->select();
                $f_id = $search_fields[0]['field_id'];
                /*查出field的field_idend*/
                $map_data['field_id'] = $f_id;
                if ($search_fields[0]['input_type'] == IT_SINGLE_TEXT || $search_fields[0]['input_type'] == IT_MULTI_TEXT || $search_fields[0]['input_type'] == IT_EDITOR) {
                    $map_data['value'] = array('like', '%' . $data_value . '%');
                } else if ($search_fields[0]['input_type'] == IT_CHECKBOX) { //处理多选框，无法实现隔项选择查询的功能
                    //重新升序整理数组
                    $value_array = explode(',', $data_value);
                    sort($value_array, SORT_NUMERIC);
                    // dump($value_array);exit;
                    $sear_value = implode(',', $value_array);
                    $map_data['value'] = array('like', '%' . $sear_value . '%');
                } else {
                    $map_data['value'] = $data_value;
                }
                if ($info_ids != null) {
                    //如果不是第一次被过滤，就需要把ids作为条件，在此基础上查询
                    $map_data['info_id'] = array('in', implode(',', $info_ids));
                }
                //进行查询
                $cat_data = D('cat_data')->where($map_data)->select();
                //更新info_ids
                if (count($cat_data) == 0) {
                    return NORES;
                }
                $info_ids = getSubByKey($cat_data, 'info_id');
            }
        }

        if ($info_ids != null) {
            //如果不为nul，意味着已经被前面的查询影响到了，就需要把过滤结果更新到条件中
            $map['info_id'] = array('in', implode(',', $info_ids));
            if ($info_ids == '') {
                return NORES; //如果发现已经受影响，并且为空
            }

        }
        //获取数据
        if ($this->_type == 'list') {
            $infos = D('Info')->getList($map, $num, $order);
            $info_count = count($infos['data']);
        } else {
            $infos = D('Info')->getLimit($map, $num, $order);
            $info_count = count($infos);
        }

        /*确定模板*/
        if ($tpl == -1) {
            //如果模板未设置，则使用entity所设定的list模板
            $tpl = $entity['use_list'];
        }
        /*确定模板end*/


        //根据获取到的模板id来渲染
        switch ($tpl) {
            case 'recom': // 推荐信息模板
                $tpl_html = W('RecomLiTpl', array('infos' => $infos, 'class' => $this, 'type' => $this->_type, 'entity' => $entity), true);
                break;
            case -1: //自动生成
                if($info_count == 0) {
                    return NORES;
                }
                $tpl_html = W('DefaultLiTpl', array('infos' => $infos, 'class' => $this->_class, 'type' => $this->_type), true);
                break;
            case 0: //解析预置模板
                if($info_count == 0) {
                    return NORES;
                }
                if(strpos($tpl,'.html')){
                    foreach($infos['data'] as &$v)
                    {
                        $v['user']=D('User')->getUserInfo($v['uid']);
                    }


                    $tpl_html=fetch('apps/cat/Tpl/default/Tpls/'.$tpl,array('infos'=>$infos));
                    break;
                }
                if($info_count == 0) {
                    return NORES;
                }
                $tpl_html = $entity['tpl_list'];
                $tpl_html = D('Render')->renderInfoLi($tpl_html, $infos, $this->_class, $this->_type);
                break;
            default: //自定义模板，通过tpl的id来确定模板
                $tpl_html = $entity['tpl' . $tpl];
                $tpl_html = D('Render')->renderInfoLi($tpl_html, $infos, $this->_class, $this->_type);

        }
        return $tpl_html;
    }


    /**自动构建模板
     * @return string
     */
    public function buildTpl()
    {
        return '';
    }

    /**
     * @param $data
     * @return mixed
     */
    public function unsetOtherParm($data)
    {
        $li = $data['map'];
        unset($li['name']);
        unset($li['entity_id']);
        unset($li['app']);
        unset($li['act']);
        unset($li['mod']);
        unset($li['p']);
        return $li;
    }
}