<?php
/**
 * Created by JetBrains PhpStorm.
 * User: 95
 * Date: 13-7-5
 * Time: 下午2:14
 * To change this template use File | Settings | File Templates.
 */

class SysTagRenderWidget extends Widget
{
    public function render($data)
    {
        $tpl_section = $data['tpl'];
        $info = $data['info'];
        $tpl_section = $this->handle($tpl_section, '{$[cTime]}', friendlyDate($info['cTime']));
        $tpl_section = $this->handle($tpl_section, '{$[cTimeD]}', date('n j', $info['cTime']));
        $tpl_section = $this->handle($tpl_section, '{$[url]}', U('cat/Index/info', array('info_id' => $info['info_id'])));


        /*用户标签*/
        $user = D('User')->getUserInfo($info['uid']);
        $tpl_section = $this->handle($tpl_section, '{$[user_avatar_big]}', $user['avatar_big']);
        $tpl_section = $this->handle($tpl_section, '{$[user_avatar_middle]}', $user['avatar_middle']);
        $tpl_section = $this->handle($tpl_section, '{$[user_avatar_small]}', $user['avatar_small']);
        $tpl_section = $this->handle($tpl_section, '{$[user_avatar_tiny]}', $user['avatar_tiny']);
        $tpl_section = $this->handle($tpl_section, '{$[user_uname]}', $user['uname']);
        $tpl_section = $this->handle($tpl_section, '{$[user_space_url]}', $user['space_url']);
        $tpl_section = $this->handle($tpl_section, '{$[user_uid]}', $user['uid']);
        $tpl_section = $this->handle($tpl_section, '{$[user_location]}', $user['location']);
        /*用户标签end*/

        $tpl_section = $this->handle($tpl_section, '{$[fav_btn]}', W('FavBtn', array('info' => $info), true));


        $entity = D('cat_entity')->find($info['entity_id']);
        if ($entity['can_over']) {
            $tpl_section = $this->handle($tpl_section, '{$[over_time]}', date('Y-m-d', $info['over_time']));
        } else {
            $tpl_section = $this->handle($tpl_section, '{$[over_time]}', '');
        }
        return $tpl_section;
    }

    /**替换文本
     * @param $rs 原字符串
     * @param $name 被替换的文字
     * @param $value 用于替换的文字
     * @return mixed
     */
    public function handle($rs, $name, $value)
    {

        $rs = str_replace($name, $value, $rs);
        return $rs;
    }
}