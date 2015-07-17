<?php
define('IT_SINGLE_TEXT', 0);
define('IT_MULTI_TEXT', 1);
define('IT_SELECT', 2);
define('IT_EDITOR', 6);
define('IT_DATE', 5);
define('IT_RADIO', 3);
define('IT_PIC', 7);
define('IT_CHECKBOX', 4);
//获取文件列表
function getFile($folder = "c:\\")
{
    //打开目录
    $fp = opendir($folder);

    //阅读目录
    while (false != $file = readdir($fp)) {
//列出所有文件并去掉'.'和'..'
        if ($file != '.' && $file != '..') {
            //$file="$folder/$file";
            $file = "$file";

            //赋值给数组
            $arr_file[] = $file;

        }
    }
    //输出结果
    if (is_array($arr_file)) {
        while (list($key, $value) = each($arr_file)) {
            $files[] = $value;
        }

    }

//关闭目录

    closedir($fp);
    return $files;

}


function getDaysPass($time)
{
    return number_format(($time - time()) / (24 * 60 * 60) + 1, 0);
}


/**带省略号的限制字符串长
 * @param $str
 * @param $num
 * @return string
 */
function getShortSp($str, $num)
{
    if (utf8_strlen($str) > $num) {
        $tag = '...';
    }
    $str = getShort($str, $num) . $tag;
    return $str;
}

function utf8_strlen($string = null)
{
// 将字符串分解为单元
    preg_match_all("/./us", $string, $match);
// 返回单元个数
    return count($match[0]);
}


/**正则表达式获取html中首张图片
 * @param $str_img
 * @return mixed
 */
function getpic($str_img)
{
    preg_match_all("/<img.*\>/isU", $str_img, $ereg); //正则表达式把图片的整个都获取出来了
    $img = $ereg[0][0]; //图片
    $p = "#src=('|\")(.*)('|\")#isU"; //正则表达式
    preg_match_all($p, $img, $img1);
    $img_path = $img1[2][0]; //获取第一张图片路径
    return $img_path;
}


/**配置函数，用于替换原有的C
 * @param $name
 * @return array
 */
function catC($name)
{
    $conf = D('Xdata')->lget('cat_Admin');
    foreach ($conf as $v) {
        if (isset($v[$name])) {
            return arrayComplie($v[$name]);
        } else {
            continue;
        }
    }

    return arrayComplie($conf);
}

/**把逗号分隔文本分解为数组
 * @param $data
 * @return array
 */
function arrayComplie($data)
{
    $rs = explode(',', $data);
    if (count($rs) == 1) {
        return $data;
    }
    return $rs;
}

/**获取配置图片
 * @param $name 配置项名
 * @return bool|string
 */
function catCP($name)
{
    $conf = D('Xdata')->lget('mag_Admin');
    foreach ($conf as $v) {
        if (isset($v[$name])) {
            return getImageUrlByAttachId($v[$name]);
        }
    }


}

/**通过信息来检查是否可阅读
 * @param $uid 用户ID
 * @param $info_id 信息ID
 * @return int
 */
function CheckCanRead($uid, $info_id)
{
    $info = D('cat_info')->find($info_id);
    return CheckCanReadEntity($uid, $info['entity_id']);
}

/**通过实体ID来检查是否可阅读
 * @param $uid
 * @param $entity_id
 * @return int
 */
function CheckCanReadEntity($uid, $entity_id)
{

    return CheckCan($uid, $entity_id, 'can_read_gid');
}

function CheckCanPostEntityN($uid, $entity_name)
{
    $map['name'] = $entity_name;
    $entity = D('cat_entity')->where($map)->limit(1)->select();
    return CheckCan($uid, $entity[0]['entity_id'], 'can_post_gid');
}

/**通过实体ID来检查是否可发布
 * @param $uid
 * @param $entity_id
 * @return int
 */
function CheckCanPostEntity($uid, $entity_id)
{

    return CheckCan($uid, $entity_id, 'can_post_gid');
}

/**通用检查权限方法
 * @param $uid
 * @param $entity_id
 * @param $can_type
 * @return int
 */
function CheckCan($uid, $entity_id, $can_type)
{
    $entity = D('cat_entity')->find($entity_id);
    $gids = explode(',', $entity[$can_type]);
    $userGroup = model('UserGroupLink')->getUserGroup($uid);
    $can_read = 0;

    if (in_array(0, $gids)) {
        $can_read = 1; //如果0在，就代表所有组均可阅读
    } else {
        foreach ($userGroup[$uid] as $key => $v) {
            if (in_array($v, $gids)) {
                $can_read = 1;
                break;
            }
        }
    }
    return $can_read;

}

function getImageSCById($id, $width = 180, $heigth = 180)
{
    $attach = model('Attach')->getAttachById($id);
    return getImageUrl($attach['save_path'] . $attach['save_name'], $width, $heigth, true);
}

function getInfoFirstValue($info_id)
{
    $info = D('cat_info')->find($info_id);
    $fileds = D('cat_field')->where('entity_id=' . $info['entity_id'])->order('sort desc')->limit(1)->select();
    $fild_first = $fileds[0];
    $value_firsts = D('cat_data')->where('info_id=' . $info['info_id'] . ' and field_id=' . $fild_first['field_id'])->select();
    if ($fileds[0]['input_type'] == IT_DATE) {
        $rs = date('Y年m月d日', $value_firsts[0]['value']);
        return $rs;
    }
    return $value_firsts[0]['value'];
}

/**调用模型字段
 * @param $info
 * @param string $value_name
 * @return mixed
 */
function rField($info, $value_name = '')
{
    $field = $info['data'][$value_name];
    $html = W('FieldRender', array('field' => $field,'only_value'=>1), true);
    return $html;
}

/**调用模型字段中用户字段
 * @param $info
 * @param string $field_name
 * @return mixed
 */
function rUser($info,$field_name='')
{
    return $info['user'][$field_name];
}
