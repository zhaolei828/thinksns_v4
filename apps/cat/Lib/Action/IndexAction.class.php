<?php
class IndexAction extends CatBaseAction
{
    public function  _initialize()
    {
        parent::_initialize();

    }

    public function index()
    {
        $this->setTitle($this->APP_NAME);
        $map['show_index'] = '1';

        $entitys = D('cat_entity')->where($map)->order('sort desc')->select();

        $this->assign('entitys', $entitys);

        $this->display();
    }

    /**
     * 发布页面
     */
    public function post()
    {
        $this->setTitle('发布信息');
        if (!($this->mid)) {
            $this->error('请登陆后发布。');
        }
        /*得到实体信息*/
        if (isset($_GET['entity_id'])) {
            $entity = D('cat_entity')->find($_GET['entity_id']);
        }
        if (isset($_GET['name'])) {
            $map['name'] = $_GET['name'];
            $entitys = D('cat_entity')->where($map)->limit(1)->select();
            $entity = $entitys[0];
        }
        $data['entity'] = $entity;
        /*得到实体信息end*/
        /*检查是否在可发布组内*/
        $can_post = CheckCanPostEntity($this->mid, $entity['entity_id']);
        if (!$can_post) {
            $this->error('对不起，您无权发布。');
        }
        /*检查是否在可发布组内end*/

        /*构建发布模板*/
        $tpl = '';
        $map_field['entity_id'] = $entity['entity_id'];
        $fields = D('cat_field')->where($map_field)->order('sort desc')->select();
        if ($data['entity']['can_over']) {
            $over_time = array('input_type' => IT_DATE, 'can_empty' => 0, 'name' => 'over_time', 'tip' => '请输入截止日期', 'alias' => '截止日期', 'args' => 'min=1&error=请选择日期');
            $tpl .= W('InputRender', array('field' => $over_time, 'info_id' => $_GET['info_id']), true);
        }

        foreach ($fields as $v) {
            $tpl .= W('InputRender', array('field' => $v, 'info_id' => $_GET['info_id']), true);
        }
        $data['tpl'] = $tpl;
        /*构建发布模板end*/

        $this->assign($data);
        $this->assign('entity_id', $entity['entity_id']);
        $this->display();
    }

    /**
     * 执行添加信息
     */
    public function doAddInfo()
    {

        /**权限认证**/
        $can_post = CheckCanPostEntity($this->mid, $_POST['entity_id']);
        if (!$can_post) {
            $this->error('对不起，您无权发布。');
        }
        /**权限认证end*/

        if (isset($_POST['info_id'])) {
            //保存逻辑
            $info = D('cat_info')->find($_POST['info_id']);
            if (isset($_POST['over_time'])) {
                $info['over_time'] = strtotime($_POST['over_time']);
            }
            $info['cTime'] = time();
            $rs_info = D('cat_info')->save($info);
            if ($rs_info) {
                $rs_info = $info['info_id'];
            } else {
                $this->error('保存失败。');
            }
        } else {
            //新增逻辑
            $info['entity_id'] = intval($_POST['entity_id']);
            $info['uid'] = $this->mid;
            //dump($_POST['over_time']);exit;

            if (isset($_POST['over_time'])) {
                $info['over_time'] = strtotime($_POST['over_time']);
            }
            $info['cTime'] = time();
            $rs_info = D('cat_info')->add($info);
        }

        $rs_data = 1;
        if ($rs_info != 0) //如果info保存成功
        {

            if (isset($_POST['info_id'])) {
                $map_data['info_id'] = $_POST['info_id'];
                D('Data')->where($map_data)->delete();
            }

            foreach ($_POST as $key => $v) {
                if ($key != 'entity_id' && $key != 'over_time' && $key != 'ignore' && $key != 'info_id') {
                    if (is_array($v)) {
                        $rs_data = $rs_data && D('Data')->addData($key, implode(',', $v), $rs_info, $_POST['entity_id']);
                    } else {
                        $rs_data = $rs_data && D('Data')->addData($key, h($v), $rs_info, $_POST['entity_id']);
                    }
                }
                if ($rs_data == 0) {
                    $this->error($key . $v);
                }
            }
            if ($rs_info && $rs_data) {
                $this->assign('jumpUrl', U('cat/Index/info', array('info_id' => $rs_info)));
                $entity = D('cat_entity')->find($info['entity_id']);
                if ($entity['need_active']) {
                    $this->success('发布成功。请耐心等待管理员审核。通过审核后该信息将出现在前台页面中。');
                } else {
                    if ($entity['show_nav']) {
                        $feed_id = D('Info')->syncToFeed($rs_info, getInfoFirstValue($rs_info), '我在' . $this->APP_NAME . '发布了一条信息。', $this->mid);
                    }
                    $this->success('发布成功。');
                }

            }
        } else {
            $this->error('发布失败。');
        }

    }

    /**
     * 详情页面
     */
    public function info()
    {

        /*检查是否在可阅读组内*/
        $can_post = CheckCanRead($this->mid, $_GET['info_id']);
        if (!$can_post) {
            $this->assign('jumpUrl', U('cat/Index/index'));
            $this->error('对不起，您无权阅读。');
        }
        /*检查是否在可阅读组内end*/
        if ($this->mid) {
            $map_read['uid'] = $this->mid;
            $map_read['info_id'] = $_GET['info_id'];

            $has_read = D('cat_read')->where($map_read)->count();
            if ($has_read) {
                D('cat_read')->where($map_read)->setField('cTime', time());
            } else {
                $map_read['cTime'] = time();
                D('cat_read')->add($map_read);
            }
        }

        /*得到实体信息*/
        $map['info_id'] = $_GET['info_id'];

        $read = D('cat_read')->where($map)->order('cTime desc')->limit(10)->select();
        foreach ($read as $key => $v) {
            $read[$key]['user'] = D('User')->getUserInfo($v['uid']);
        }

        $info = D('cat_info')->find($_GET['info_id']);

        $this->setTitle(getInfoFirstValue($info['info_id']));
        $info['read']++;
        D('cat_info')->save($info);
        $entity = D('cat_entity')->find($info['entity_id']);
        $assign['info'] = $info;
        // dump($info);exit;
        $assign['entity'] = $entity;
        //取出全部的字段数据
        $map_field['entity_id'] = $entity['entity_id'];
        $fields = D('cat_field')->where($map_field)->order('sort desc')->select();
        //确定是否过期
        $now = time();
        if ($now > $info['over_time']) {
            $overed = '1';
            $assign['overed'] = 1;
        }
        //获取到信息的数据
        $info_data = D('Data')->getByInfoId($info['info_id']);
        /*得到实体信息end*/
        $tpl = '';
        /*构建自动生成模板*/
        $assign['fields'] = $fields;


        $tpl = W('SysTagRender', array('tpl' => $tpl, 'info' => $info), true);
        $assign['tpl'] = $tpl;
        $assign['info'] = $info;
        $info['reads'] = $read;
        if ($entity['use_detail'] == 0) {
            $detail = W('DefaultInfoTpl', array('fields' => $fields, 'info' => $info), true);
        } else {

            /**默认模板添加**/
            $assign['entity'] = D('cat_entity')->find($info['entity_id']);
            $assign['data'] = D('Data')->getByInfoId($info['info_id']);
            $assign['user'] = D('User')->getUserInfo($info['uid']);
            $assign['info_id'] = $info['info_id'];
            $assign['info']['com'] = D('Com')->getList($map, 5);
            $assign['mid']=$this->mid;
            /**默认模板添加end**/

            $detail =  fetch('apps/cat/Tpl/default/Tpls/' . $entity['use_detail'], $assign);
        }
        $assign['detail'] = $detail;








        $this->assign($assign);
        $this->display();
    }

    /**
     * 列表页面
     */
    public function li()
    {
        /* G('begin');*/
        if (isset($_GET['entity_id'])) {
            $map['entity_id'] = $_GET['entity_id'];
        }
        if (isset($_GET['name'])) {
            $map['name'] = $_GET['name'];
        }


        $entitys = D('cat_entity')->where($map)->limit(1)->select();
        $entity = $entitys[0];
        $this->setTitle($entity['alias']);
        // dump($entity);
        //exit;
        $map_s_field['entity_id'] = $entity['entity_id'];
        $map_s_field['can_search'] = '1';
        $search_fields = D('cat_field')->where($map_s_field)->order('sort desc')->select();
        foreach ($search_fields as $key => $v) {
            $search_fields[$key]['values'] = json_decode($v['option'], true);
        }
        // dump($search_fields);exit;
        $data['search_fields'] = $search_fields;
        $this->assign($data);

        /*        G('end');
                $used = G('begin', 'end', 6);
                $this->assign('used', $used);*/

        $this->display();
    }

    public function _doFav()
    {

        /* if (D('Info')->checkOwner($this->mid, $_POST['id'])) {
             $this->ajaxReturn('', '', 3);
         }*/

        if (!D('Fav')->checkFav($this->mid, $_POST['id'])) {
            //未收藏，就收藏
            if (D('Fav')->doFav($this->mid, $_POST['id'])) {
                $this->ajaxReturn('', '', 1);
            };
        } else {
            //已收藏，就取消收藏
            if (D('Fav')->doDisFav($this->mid, $_POST['id'])) {
                $this->ajaxReturn('', '', 2);
            };

        }

        $this->ajaxReturn('', '', 0);
    }

    /**
     * 支持ajax删除信息
     */
    public function _delInfo()
    {

        $map['info_id'] = $_POST['info_id'];
        $rs = D('cat_info')->where($map)->delete();
        D('cat_data')->where($map)->delete();
        D('cat_com')->where($map)->delete();
        if ($rs) {
            $this->ajaxReturnS();
        } else {
            $this->ajaxReturnF();
        }
    }

    /**
     * 向$userNames发送@消息。
     * @param $comment
     * @param $userNames
     */
    private function sendAtmeNotify($comment, $userNames, $sender)
    {
        $sender = D('User')->getUserInfo($comment['uid']);
        array_unique($userNames);
        foreach ($userNames as $username) {
            $user = D('User')->getUserInfoByName($username);
            $message = array(
                'site' => '分类信息',
                'name' => $sender['uname'],
                'feed_url' => U('cat/Index/info', array('info_id' => $comment['info_id'])),
                'face' => $sender['avartar_small'],
                'space_url' => $sender['space_url'],
                'content' => $comment['content'],
                'publish_time' => friendlyDate($comment['cTime']),
            );
            if ($user) {
                $result = D('Notify')->sendNotify($user['uid'], 'mxcat_atme', $message, $comment['uid']);
            }
        }
    }

    private function addLinkToAtme($content)
    {
        $REGEX = "/(@.+?)([\s|:<]|$)/is";
        preg_match_all($REGEX, $content, $matches, PREG_OFFSET_CAPTURE);
        $result = $content;
        foreach (array_reverse($matches[1]) as $match) {
            $text = $match[0];
            $offset = $match[1];
            $username = substr($text, 1);
            $user = D('User')->getUserInfoByName($username);
            if ($user) {
                $uid = $user['uid'];
                $href = '<a target="_blank" event-node="face_card" uid="' . $uid . '">' . $text . '</a>';
                $result = substr_replace($result, $href, $offset, sizeof($text));
            }
            /* dump($uid);*/
        }
        return $result;
    }

    /**
     * 支持ajax评论
     */
    public function _doCom()
    {
        $com['uid'] = $this->mid;
        $com['cTime'] = time();
        $com['info_id'] = $_POST['info_id'];
        $com['content'] = h($_POST['content']);
        $com['content'] = $this->addLinkToAtme($com['content']);
        $rs = D('cat_com')->add($com);
        $user = D('User')->getUserInfo($this->mid);
        $sender = $user;
        if ($rs) {
            $match = preg_match_all("/@(.+?)([\s|:<]|$)/is", $com['content'], $matches);
            $this->sendAtmeNotify($com, $matches[1], $sender);
            $text = '
            <li class="clearfix underline pd10">
                                <div style="float: left;margin-right: 10px;width:50px;">
                                    <a event-node="face_card" uid="' . $user['uid'] . '" href="' . $user['space_url'] . '">
                                        <img src="' . $user['avatar_small'] . '">
                                    </a>
                                </div>
                                <div class="left cgrey " >
                                    <div class="mb10 cgrey lh14">
                                        <a event-node="face_card" uid="1"
                                           href="' . $user['space_url'] . '">
                                            ' . $user['uname'] . ' </a>
                                        <span class="c333"> &nbsp;&nbsp;&nbsp;评论于：刚刚</span>
                                    </div>
                                    <div>
                                        <p class="c333 lh18">' . h($com['content']) . '</p>
                                    </div>
                                </div>

                            </li>
            ';
            $this->ajaxReturn($text, '', 1);
        } else {
            $this->ajaxReturnF();
        }
    }

    public function _doScore()
    {
        $rate['info_id'] = $_POST['info_id'];
        $rate['uid'] = $this->mid;
        $map = $rate;
        if (D('cat_rate')->where($map)->count()) {
            $this->ajaxReturnF();
        }

        $rate['score'] = $_POST['score'];
        $rate['cTime'] = time();
        $rs = D('cat_rate')->add($rate);
        if ($rs) {


            $map_info['info_id'] = $_POST['info_id'];
            $count = D('cat_rate')->where($map_info)->Avg('score');
            D('cat_info')->where($map_info)->setField('rate', $count);

            $this->ajaxReturn($count, null, 1);
        } else {
            $this->ajaxReturnF();
        }

    }

    /**
     * 获取自己用于发送的信息列表
     */
    public function _get_s_infos()
    {
        $infos = D('Info')->getList('entity_id=' . $_GET['entity_id'] . ' and uid=' . $this->mid);

        $this->assign('send_infos', $infos);
        $this->display();
    }

    /**发送信息
     *
     */
    public function _send_info()
    {
        $send = D('cat_send')->create();
        $send['send_uid'] = $this->mid;
        $send['rec_uid'] = $_POST['uid'];

        $send['cTime'] = time();
        $rs = D('cat_send')->add($send);

        if ($rs) {
            $this->ajaxReturn('', '', 1);
        }
        $this->ajaxReturn('', '', 0);

    }

    /**
     * 动态css输出
     */
    public function css()
    {
        header( 'Content-Type:text/css;charset=utf-8' );
        echo catC('CSS');
    }

    /**
     * 动态JS输出
     */
    public function js()
    {
        header('Content-type: text/javascript');
        echo catC('JS');

    }

}