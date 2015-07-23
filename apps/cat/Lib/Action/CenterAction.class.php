<?php
class CenterAction extends CatBaseAction
{
    public function _initialize()
    {
        if ($this->mid == 0) {
            $this->error('请登录后使用个人中心。');
        }
        parent::_initialize();
    }

    public function _doSendInfo()
    {
        $send = D('cat_send')->create();
        $send['send_uid'] = $this->mid;
        $send['cTime'] = time();
        $rec_uids = explode(',', $_POST['rec_uids']);
        $rs = 1;
        foreach ($rec_uids as $key => $v) {
            $t_send = $send;
            $t_send['rec_uid'] = $v;
            $rs = $rs && D('cat_send')->add($t_send);

        }
        if($rs)
        {
            $this->success('发送成功。');
        }
        else{
            $this->error('发送失败。');
        }

    }

    public function my()
    {
        $this->setTitle('个人中心');

        $entitys = D('cat_entity')->where('show_nav = 1')->select();
        $map['entity_id']=$entitys[0]['entity_id'];
        //获取该用户发布的全部用户组
        $map['uid'] = $this->mid;
        if (isset($_GET['id'])) {
            $map['entity_id'] = $_GET['id'];
        }


        $my_post = D('cat_info')->where($map)->findPage(10);
        foreach ($entitys as $key => $v) {
            if (!CheckCanReadEntity($this->mid, $v['entity_id'])) {
                unset($entitys[$key]);
            }
        }
        $data['entitys'] = $entitys;

        //$tpl_html =W('DefaultLiTpl',array('infos'=>$infos,'class'=>$this->_class,'type'=>$this->_type),true);

        foreach ($my_post['data'] as $key => $info) {

            $my_post['data'][$key]['tpl'] = D('Render')->renderInfo($info['info_id']);
        }

        $data['my_post'] = $my_post;
        $this->assign('current_entity',$map['entity_id']);
        $this->assign($data);
        $this->display();

    }

    public function rec()
    {
        $this->setTitle('收到的信息');
        //从缓存中获取
        $rec = S('cat_center_rec_'.$this->mid);
        if (empty($rec)) {
            $map['rec_uid'] = $this->mid;
            $rec = D('Send')->getList($map);
            S('cat_center_rec'.$this->mid, $rec, 10);
        }
        $this->assign('rec', $rec);
        $this->display();
    }

    public function send()
    {
        $this->setTitle('发送的信息');
        //从缓存中获取
        $rec = S('cat_center_send'.$this->mid);
        if (empty($rec)) {
            $map['send_uid'] = $this->mid;
            $rec = D('Send')->getList($map);
            S('cat_center_send'.$this->mid, $rec, 10);
        }
        $this->assign('rec', $rec);
        $this->display();
    }

    public function post()
    {
        $this->setTitle('发送信息');
        $entitys = D('cat_entity')->findAll();
        $first_infos = D('cat_info')->where('entity_id =' . $entitys[0]['entity_id'] . ' and uid=' . $this->mid)->select();
        $this->assign('first_infos', $first_infos);
        $this->assign('entitys', $entitys);
        $this->display();
    }

    public function _get_infos()
    {
        $map['entity_id'] = $_GET['entity_id'];
        $map['uid'] = $this->mid;
        $infos = D('cat_info')->where($map)->select();
        $this->assign('infos', $infos);
        $this->display();
    }

    public function _doGetBack()
    {
        $map['send_id'] = $_POST['send_id'];
        $map['send_uid'] = $this->mid;
        $rs = D('cat_send')->where($map)->delete();
        S('cat_center_rec', null);
        $this->quickReturn($rs);
    }

    public function _doRead()
    {
        $map['send_id'] = $_POST['send_id'];
        S('cat_center_rec_'.$this->mid,null);
        S('cat_havent_read' . $this->mid, null);
        D('cat_send')->where($map)->setField('readed', 1);
    }

    public function fav()
    {
        $this->setTitle('个人中心');

        $entitys = D('cat_entity')->where('show_nav = 1')->select();
        $map['entity_id']=$entitys[0]['entity_id'];

        //获取该用户发布的全部用户组
        $t_map['uid'] = $this->mid;
        $fav = D('cat_fav')->where($t_map)->findAll();
        $fav_ids = getSubByKey($fav, 'info_id');
        $map['info_id'] = array('in', implode(',', $fav_ids));
        // dump($fav_ids);exit;
        if (isset($_GET['id'])) {
            $map['entity_id'] = $_GET['id'];
        }
        $my_post = D('cat_info')->where($map)->findPage(10);
        $entitys = D('cat_entity')->where('show_nav = 1')->select();
        foreach ($entitys as $key => $v) {
            if (!CheckCanReadEntity($this->mid, $v['entity_id'])) {
                unset($entitys[$key]);
            }
        }
        $data['entitys'] = $entitys;

        foreach ($my_post['data'] as $key => $info) {
            $my_post['data'][$key]['tpl'] = D('Render')->renderInfo($info['info_id']);
        }

        $data['my_post'] = $my_post;
        // dump($my_post);exit;
        $this->assign('current_entity',$map['entity_id']);
        $this->assign($data);
        $this->display();

    }
}