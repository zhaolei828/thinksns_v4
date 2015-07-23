<?php
/*
 * 最基础的类，实现对默认应用名的支持
 */
class CatBaseAction extends Action
{

    protected $view = null;
    protected $APP_NAME = '';

    public function  _initialize()

    {
        $APP_NAME = $this->app['app_alias'];
        $this->APP_NAME = $APP_NAME;
        $data['APP_NAME'] = $APP_NAME;

        $havent_read = S('cat_havent_read' . $this->mid);
        if (empty($havent_read)) {
            $map['rec_uid'] = $this->mid;
            $map['readed'] = 0;
            $havent_read = D('cat_send')->where($map)->count();
            S('cat_havent_read' . $this->mid, $havent_read, 60);
        }
        $data['havent_read'] = $havent_read;
        $this->assign($data);

    }

    public function ajaxReturnS()
    {
        $this->ajaxReturn(null, null, 1);
    }

    public function ajaxReturnF()
    {
        $this->ajaxReturn(null, null, 0);
    }

    public function quickReturn($rs)
    {
        if ($rs) {
            $this->ajaxReturnS();
        } else {
            $this->ajaxReturnF();
        }
    }
}