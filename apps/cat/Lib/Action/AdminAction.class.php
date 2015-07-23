<?php
tsload(APPS_PATH . '/admin/Lib/Action/AdministratorAction.class.php');
class AdminAction extends AdministratorAction
{
    private $_model_category;

    public function _initialize()
    {
        $this->pageTitle['index'] = '管理首页';
        $this->pageTitle['entity'] = '实体管理';
        $this->pageTitle['field'] = '字段管理';
        $this->pageTitle['addEntity'] = '新增实体';
        $this->pageTitle['addField'] = '新增字段';
        $this->pageTitle['cat'] = '分类目录(用于自定义级联菜单)';
        $this->pageTitle['com'] = '评论管理';
        $this->pageTitle['info'] = '信息管理';
        $this->pageTitle['addAd']='添加广告位';
        $this->pageTitle['ads']='广告位管理';
        $this->_model_category = model('CategoryTree')->setTable('cat');

        parent::_initialize();
    }
    /*广告应用整合部分*/

    public function _get_ad_type() {
        return array(1 => 'HTML', 2 => '代码', 3 => '轮播图');
    }
    public function doDelAd() {
        $ad_id = $_POST['ad_id'];
        D('MxAd')->doDelAdSpace($ad_id);
        $this->ajaxReturn(array(), null, '1');
    }

    public function addAd() {
        $this->pageTab[] = array('title' => '添加广告', 'tabHash' => 'addAd', 'url' => U('cat/Admin/addAd'));
        // Get the ad to edit.
        $ad_id = $_GET['ad_id'];
        if(intval($ad_id) != 0) {
            $data = D('MxAd')->getAdSpace($ad_id);

        } else {
            $data['display_type'] = 1;
        }

        // Get ad places
        $places = D('MxAdPlace')->getAllPlaces();
        foreach($places as $e)  {
            $place_opt[$e['name']] = "$e[name] $e[description]";
        }
        // Generate page
        $this->initMenu();
        $this->pageKeyList = array('ad_id', 'title', 'place', 'is_active', 'is_closable', 'ctime', 'mtime', 'display_order', 'display_type', 'content_html', 'content_code');
        for($i=0;$i<6;$i++) {
            $this->pageKeyList[] = "content_picture$i";
            $this->pageKeyList[] = "content_link$i";
        }
        $this->pageKeyList[] = 'hit';
        $this->notEmpty = array('title', 'content_html', 'content_code', 'content_picture0');
        $this->opt['place'] = $place_opt;
        $this->opt['is_active'] = array(0 => '不活动', 1 => '活动');
        $this->opt['is_closable'] = array(0 => '不可关闭', 1 => '可关闭');
        $this->opt['display_type'] = $this->_get_ad_type();
        $this->savePostUrl = U('cat/Admin/doAddAd');
        // Show page
        $data['ctime']=friendlyDate($data['ctime']);
        $data['mtime']=friendlyDate($data['mtime']);

        $this->displayConfig($data);
    }

    public function doAddAd() {
        $is_edit = intval($_POST['ad_id']) != 0;
        $ad = D('adspace_ad')->create();
        if(!$is_edit) $ad['ctime'] = time();
        $ad['mtime'] = time();
        $rs = $is_edit ? D('adspace_ad')->save($ad) : D('adspace_ad')->add($ad);
        if($rs) {
            $this->success($is_edit ? '保存成功' : '添加成功');
        } else {
            $this->error($is_edit ? '保存失败' : '添加失败');
        }
    }
    public function ads(){
        $this->pageKeyList = array('ad_id', 'title', 'place', 'is_active', 'is_closable', 'ctime', 'mtime', 'display_order', 'display_type', 'content_html', 'content_code');
        $this->pageButton[] = array('title' => '新增广告', 'onclick' => 'location.href=\'' . U('cat/Admin/addAd') . '\'');

        for($i=0;$i<6;$i++){
            $this->pageKeyList[] = "content_picture$i";
            $this->pageKeyList[] = "content_link$i";
        }

        $this->pageKeyList[] = 'hit';
        $this->pageKeyList[] = 'DOACTION';
        $ads = model('MxAd')->getAdSpaceList();
        // 解释数据
        foreach($ads['data'] as $i => $ad){
            // $ad = $ads['data'][$i];
            $ads['data'][$i]['is_active'] = $ad['is_active'] != 0 ? '是' : '否';
            $ads['data'][$i]['is_closable'] = $ad['is_closable'] != 0 ? '可关闭' : '不可关闭';
            $ads['data'][$i]['ctime'] = $ad['ctime'] ? date( 'Y-m-d H:i:s',$ad['ctime']) : '暂无数据';
            $ads['data'][$i]['mtime'] = $ad['mtime'] ? date( 'Y-m-d H:i:s',$ad['mtime']) : '暂无更新';
            $AD_TYPE = $this->_get_ad_type();
            $ads['data'][$i]['display_type'] = $AD_TYPE[$ad['display_type']];
            $ads['data'][$i]['DOACTION'] =
                '<a href="' . U('cat/Admin/addAd', array('ad_id' => $ad['ad_id'])) . '">编辑</a> | ' .
                '<a onclick="admin.delAd(' . $ad['ad_id'] . ',\'“' . $ad['alias'] . '”\')">删除</a>';
        }
        $this->initMenu();
        $this->displayList($ads);
    }

    /*广告应用整合部分end*/



    public function initMenu()
    {
        $this->pageTab[] = array('title' => '基本配置', 'tabHash' => 'index', 'url' => U('cat/Admin/index'));
        $this->pageTab[] = array('title' => '分类管理', 'tabHash' => 'cat', 'url' => U('cat/Admin/cat'));
        $this->pageTab[] = array('title' => '实体管理', 'tabHash' => 'entity', 'url' => U('cat/Admin/entity'));
        $this->pageTab[] = array('title' => '信息管理', 'tabHash' => 'info', 'url' => U('cat/Admin/info'));
        $this->pageTab[] = array('title' => '评论管理', 'tabHash' => 'com', 'url' => U('cat/Admin/com'));
        $this->pageTab[] = array('title' => '广告管理', 'tabHash' => 'ads', 'url' => U('cat/Admin/ads'));
      /*$this->pageTab[] = array('title' => '添加广告', 'tabHash' => 'addAd', 'url' => U('cat/Admin/addAd'));
        $this->pageTab[] = array('title' => '自助升级', 'tabHash' => 'update', 'url' => U('cat/Admin/update'));*/
        $this->pageTab[]=array('title'=>'勉励捐助','tabHash'=>'support','url'=>U('cat/Admin/support'));
        $this->pageTab[] = array('title' => '想天工作室', 'tabHash' => 'online', 'url' => U('cat/Admin/online'));

    }


    public function doUpdate()
    {
        $sql_file = APPS_PATH . '/cat/update2.sql';
        $res = D('')->executeSqlFile($sql_file);
        model('Lang')->createCacheFile('PUBLIC', 0);
        if (!empty($res)) {
            echo $res['error_code'];
            echo '<br />';
            echo $res['error_sql'];
            // 清除已导入的数据
            exit;
        } else {
            echo '升级成功。';
        }


    }

    /**信息管理页面
     *
     */
    function info()
    {

        $this->initMenu();
        if (isset($_GET['entity_id'])) {
            $map['entity_id'] = $_GET['entity_id'];
            $data = D('cat_info')->where($map)->order('cTime desc')->findPage(10);
            $entity = D('cat_entity')->find($_GET['entity_id']);
        } else {
            $data = D('cat_info')->order('active asc,cTime desc')->findPage(10);
        }
        $this->_listpk = 'info_id';
        $this->pageButton[] = array('title' => '删除', 'onclick' => 'admin.delInfos();');

        if ($entity['need_active']) {
            $this->pageKeyList = array('info_id', 'entity_id', 'entity_alias', 'value', 'active', 'top', 'recom', 'do');
        } else {
            $this->pageKeyList = array('info_id', 'entity_id', 'entity_alias', 'value', 'top', 'recom', 'do');
        }
        foreach ($data['data'] as $key => $v) {
            $entity = D('cat_entity')->find($v['entity_id']);
            $data['data'][$key]['entity_alias'] = $entity['alias'];

            $data['data'][$key]['active'] = $v['active'] == 1 ? '已审核' : '未审核';

            $data['data'][$key]['value'] = '<a target="_blank" href="' . U('cat/Index/info', array('info_id' => $v['info_id'])) . '">' . getInfoFirstValue($v['info_id']) . '</a>';
            $data['data'][$key]['do'] = ' <a onclick="admin.delInfo(' . $v['info_id'] . ',\'“' . t($data['data'][$key]['value']) . '”\')">删除</a>';
            $data['data'][$key]['top'] = $data['data'][$key]['top'] ?
                '已置顶<a onclick="admin.changeTopmost(' . $v["info_id"] . ',0)" target="_blank">取消</a>' :
                '未<a onclick="admin.changeTopmost(' . $v["info_id"] . ',1)" target="_blank">置顶</a>';
            $data['data'][$key]['recom'] = $data['data'][$key]['recom'] ?
                '已推荐<a onclick="admin.changeRecom(' . $v['info_id'] . ',0)" target="_blank">取消</a>' :
                '未<a onclick="admin.changeRecom(' . $v["info_id"] . ',1)" target="_blank">推荐</a>';
            if ($entity['need_active']) {
                if ($v['active'] == 0) {
                    $data['data'][$key]['do'] .= ' | <a onclick="admin.active(' . $v['info_id'] . ',\'“' . t($data['data'][$key]['value']) . '”\')">通过审核</a>';
                } else {
                    $data['data'][$key]['do'] .= ' | <a onclick="admin.unactive(' . $v['info_id'] . ',\'“' . t($data['data'][$key]['value']) . '”\')">驳回审核</a>';
                }

            }

        }
//        $this->pageButton[] = array('title' => '', 'onclick' => 'location.href=\'' . U('cat/Admin/addField', array('entity_id' => $_GET['entity_id'])) . '\'');
        $this->displayList($data);
    }

    public function doDelInfos()
    {
        if (empty($_POST['info_id'])) {
            $return['status'] = 0;
            $return['data'] = '';
            echo json_encode($return);
            exit();
        }
        !is_array($_POST['info_id']) && $_POST['info_id'] = array($_POST['info_id']);
        $data['info_id'] = array('in', $_POST['info_id']);
        $result = D('cat_info')->where($data)->delete();

        $result1 = D('cat_data')->where($data)->delete();
        D('cat_com')->where($data)->delete();
        D('cat_fav')->where($data)->delete();
        D('cat_send')->where($data)->delete();
        D('cat_read')->where($data)->delete();
        D('cat_rate')->where($data)->delete();
        if ($result && $result1) {
            $return['status'] = 1;
            $return['data'] = '删除成功。';
        } else {
            $return['status'] = 0;
            $return['data'] = '删除失败。';
        }
        echo json_encode($return);
        exit();
    }

    function  doActive()
    {
        $info = D('cat_info')->create();
        D('cat_info')->save($info);

    }

    /**
     * 字段列表
     */
    function field()
    {
        $this->pageTab[] = array('title' => '字段管理', 'tabHash' => 'field', 'url' => U('cat/Admin/field', array('entity_id' => $_GET['entity_id'])));
        $this->initMenu();
        $data = D('cat_field')->where('entity_id=' . $_GET['entity_id'])->order('sort desc')->findPage(10);
        $this->pageKeyList = array('field_id', 'name', 'alias', 'sort', 'input_type', 'do');

        $type_alias = array(IT_SINGLE_TEXT => '单行文本', IT_MULTI_TEXT => '多行文本', IT_SELECT => '下拉框', IT_RADIO => '单选框', IT_EDITOR => '编辑器', IT_PIC => '单图上传');
        foreach ($data['data'] as $key => $v) {
            $data['data'][$key]['do'] = '<a href="' . U('cat/Admin/addField', array('field_id' => $v['field_id'])) . '">编辑</a> | <a onclick="admin.delField(' . $v['field_id'] . ',\'“' . $v['alias'] . '”\')">删除</a>';
            $data['data'][$key]['input_type'] = $type_alias[$v['input_type']];
        }
        $this->pageButton[] = array('title' => '新增字段', 'onclick' => 'location.href=\'' . U('cat/Admin/addField', array('entity_id' => $_GET['entity_id'])) . '\'');
        $this->displayList($data);
    }


    /**
     * 添加字段页面
     */
    function addField()
    {
        $this->pageTab[] = array('title' => '新增字段', 'tabHash' => 'addField', 'url' => U('cat/Admin/addField', array('field_id' => $_GET['field_id'], 'entity_id' => $_GET['entity_id'])));
        $data = $_GET;
        if (intval($_GET['field_id']) != 0) {
            $data = D('cat_field')->find($_GET['field_id']);
        }

        /* $this->pageTab[] = array('title' => '新增实体', 'tabHash' => 'addEntity', 'url' => U('cat/Admin/addEntity'));*/
        $this->pageKeyList = array('field_id', 'entity_id', 'name', 'can_search', 'alias', 'input_type', 'args', 'option', 'tip', 'over_hidden', 'sort', 'default_value', 'can_empty');
        $this->initMenu();
        $this->notEmpty = array('name', 'alias', 'entity_id', 'input_type', 'can_search');
        $this->opt['input_type'] = array(0 => '单行文本', '多行文本', '下拉框', '单选框', '多选框', '日期选择', '编辑器', '单张图片上传'); //, '附件上传', '颜色选择', '地区选择'
        $this->opt['can_search'] = array(0 => '禁止搜索', '允许搜索');
        $this->opt['can_empty'] = array(0 => '必填', '选填');
        $this->opt['over_hidden'] = array(0 => '不影响', '自动隐藏');


        $cat = D('cat_entity')->select();
        foreach ($cat as $v) {
            $this->opt['entity_id'][$v['entity_id']] = $v['alias'];
        }

        $this->savePostUrl = U('cat/Admin/doAddField');
        $this->displayConfig($data);
    }

    /**
     * 执行添加字段
     */
    function doAddField()
    {
        $cant_use_name = array('name', 'app', 'act', 'mod', 'entity_id', 'field_id');
        if (in_array($_POST['name'], $cant_use_name)) {
            $this->error('不能使用此字段名，此字段名被系统保留！');
        }
        if (intval($_POST['field_id']) != 0) {
            $field = D('cat_field')->create();
            $rs = D('cat_field')->save($field);
            if ($rs) {
                $this->success('保存成功。');
            } else {
                $this->error('保存失败。');
            }
        } else {
            $field = D('cat_field')->create();
            $rs = D('cat_field')->add($field);
            if ($rs) {
                $this->success('添加成功。');
            } else {
                $this->error('添加失败。');
            }
        }
    }

    /**
     * 首页
     */
    function index()
    {
        $this->initMenu();
        $this->pageKeyList = array('CSS', 'JS');
        $this->displayConfig();
    }

    /**
     * 分类目录页面
     */
    function cat()
    {
        $this->initMenu();
        $_GET['pid'] = intval($_GET['pid']);
        $treeData = $this->_model_category->getNetworkList();
        $delParam['app'] = 'cat';
        $delParam['module'] = 'Admin';
        $delParam['method'] = 'deleteCat';
        $this->displayTree($treeData, 'cat', 1, $delParam);
    }

    /**
     * 实体页面
     */
    function entity()
    {
        $this->initMenu();

        $data = D('cat_entity')->findPage(10);
        $this->pageKeyList = array('entity_id', 'name', 'alias', 'do');
        foreach ($data['data'] as $key => $v) {
            $data['data'][$key]['do'] =
                '<a href="' . U('cat/Admin/info', array('entity_id' => $v['entity_id'])) . '">查看信息</a> | ' .
                '<a href="' . U('cat/Admin/field', array('entity_id' => $v['entity_id'])) . '">字段</a>' .
                ' | ' . '<a href="' . U('cat/Admin/addEntity', array('entity_id' => $v['entity_id'])) . '">编辑</a> | ' .
                '<a onclick="admin.delEntity(' . $v['entity_id'] . ',\'“' . $v['alias'] . '”\')">删除</a>';
        }
        $this->pageButton[] = array('title' => '新增实体', 'onclick' => 'location.href=\'' . U('cat/Admin/addEntity') . '\'');
        $this->displayList($data);
    }

    /**
     * 添加实体页面
     */
    function addEntity()
    {

        if (intval($_GET['entity_id']) != 0) {
            $data = D('cat_entity')->find($_GET['entity_id']);

        }
        $this->pageTab[] = array('title' => '新增实体', 'tabHash' => 'addEntity', 'url' => U('cat/Admin/addEntity', array('entity_id' => $_GET['entity_id'])));
        $this->pageKeyList = array('entity_id', 'name', 'alias', 'show_nav', 'show_post', 'pb_icon', 'show_index', 'need_active', 'can_rec', 'rec_entity', 'sort', 'can_post_gid', 'can_read_gid', 'can_over', 'use_detail', 'use_list', 'tpl_list', 'tpl1', 'tpl2', 'tpl3', 'des1', 'des2', 'des3');
        $this->initMenu();
        $this->notEmpty = array('name', 'alias', 'use_detail', 'use_list');
        $cat = D('cat')->order('sort asc')->select();
        foreach ($cat as $v) {
            $this->opt['cat'][$v['cat_id']] = $v['title'];
        }
        $this->opt['can_rec'] = array(0 => '不显示', '显示');

        $path='apps/cat/Tpl/default/Tpls';
        $dir=getFile($path);

        $dir_file=array();;
        foreach($dir as $k=>$v)
        {
            $dir_file[$v]=$v;
        }
        $this->opt['use_detail'] =array(0 => '自动生成',1=>'——以下为模板文件——')+$dir_file;



        $this->opt['use_list'] = array(-1 => '自动生成', 0 => '默认模板', 1 => '自定义模板1', 2 => '自定义模板2', 3 => '自定义模板3',-2=>'——以下为模板文件——')+$dir_file;



        $this->opt['can_over'] = array(0 => '禁止', 1 => '允许');
        $this->opt['show_post'] = array(0 => '不显示', '显示');
        $this->opt['show_nav'] = array(0 => '不显示', '显示');
        $this->opt['show_index'] = array(0 => '不显示', '显示');
        $this->opt['need_active'] = array(0 => '无需审核', '需要审核');
        $this->savePostUrl = U('cat/Admin/doAddEntity');
        $this->displayConfig($data);
    }

    /**
     * 执行添加字段
     */
    function doAddEntity()
    {
        if (intval($_POST['entity_id']) != 0) {
            $entity = D('cat_entity')->create();
            $rs = D('cat_entity')->save($entity);
            if ($rs) {
                $this->success('保存成功。');
            } else {
                $this->error('保存失败。');
            }
        } else {
            $entity = D('cat_entity')->create();
            $rs = D('cat_entity')->add($entity);
            if ($rs) {
                $this->success('添加成功。');
            } else {
                $this->error('添加失败。');
            }
        }

    }

    /**
     * 评论页面
     */
    function com()
    {
        $this->initMenu();
        $this->pageKeyList = array('com_id', 'user', 'info', 'content', 'cTime', 'Do');
        $data = D('Com')->getList('', 10);
        foreach ($data['data'] as $key => $com) {
            $user = D('User')->getUserInfo($com['uid']);
            $data['data'][$key]['user'] = $user['uname'];
            $data['data'][$key]['info'] = '<a target="_blank" href="' . U('cat/Index/info', array('info_id' => $com['info_id'])) . '">查看所在信息</a>';
            $data['data'][$key]['content'] = getShort($com['content'], 40);
            $data['data'][$key]['cTime'] = friendlyDate($com['cTime']);
            $data['data'][$key]['Do'] = '<a onclick="admin.delCom(' . $com['com_id'] . ')">删除</a>';
        }
        $this->displayList($data);
    }

    function doDelCom()
    {
        $map['com_id'] = $_POST['com_id'];
        $this->doDel('cat_com', $map);
    }

    /**
     * 删除字段
     */
    function doDelField()
    {

        $map['field_id'] = $_POST['field_id'];
        D('cat_data')->where($map)->delete();
        $this->doDel('cat_field', $map);

    }

    /**
     * 删除实体
     */
    function doDelEntity()
    {
        $map['entity_id'] = $_POST['entity_id'];
        //删除关联信息
        D('cat_info')->where($map)->delete();
        $this->doDel('cat_entity', $map);
    }

    function doDelInfo()
    {
        $map['Info_id'] = $_POST['info_id'];
        $this->doDel('cat_info', $map);
    }

    /**删除的通用方法
     * @param $tableName 表明
     * @param $map 条件
     */
    function doDel($tableName, $map)
    {
        $rs = D($tableName)->where($map)->delete();
        if ($rs) {
            $this->ajaxReturn('', '', 1);
        } else {
            $this->ajaxReturn('', '', 0);
        }
    }

    /**
     * 改变置顶设置
     * @return mixed
     */
    function doTopmost()
    {
        $info['info_id'] = $_POST['info_id'];
        $info['top'] = $_POST['topmost'];
        $res = D('cat_info')->save($info);
        return $this->ajaxReturn('', '', $res);
    }

    /**
     * 改变推荐设置
     * @return mixed
     */
    function doRecom()
    {
        $info['info_id'] = $_POST['info_id'];
        $info['recom'] = $_POST['recom'];
        $res = D('cat_info')->save($info);
        return $this->ajaxReturn('', '', $res);
    }
}
