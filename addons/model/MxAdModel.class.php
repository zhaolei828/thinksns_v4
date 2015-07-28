<?php
/**
 * Created by JetBrains PhpStorm.
 * User: caipeichao
 * Date: 8/27/13
 * Time: 6:52 PM
 * To change this template use File | Settings | File Templates.
 */

class MxAdModel extends model {
    protected $tableName = 'adspace_ad';
    protected $_error;

    /**
     * 添加广告位数据
     * @param array $data 广告位相关数据
     * @return boolean 是否插入成功
     */
    public function doAddAdSpace($data)
    {
        $data['display_order'] = $this->count();
        $res = $this->add($data);

        return (boolean)$res;
    }

    /**
     * 获取广告位列表数据
     * @return array 广告位列表数据
     */
    public function getAdSpaceList()
    {
        $data = $this->order('display_order DESC')->findPage(1000);
        return $data;
    }

    public function getAdSpaceListAtPlace($place_name) {
        $where['place'] = $place_name;

        $data = $this->where($where)->order('display_order DESC')->findAll();

        return $data;
    }

    /**
     * 删除广告位操作
     * @param string|array $ids 广告位ID
     * @return boolean 是否删除广告位成功
     */
    public function doDelAdSpace($ids)
    {
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        if(empty($ids)) {
            return false;
        }
        $map['ad_id'] = array('IN', $ids);
        $res = $this->where($map)->delete();

        return (boolean)$res;
    }

    /**
     * 获取指定ID的广告位信息
     * @param integer $id 广告位ID
     * @return array 指定ID的广告位信息
     */
    public function getAdSpace($id)
    {
        if(empty($id)) {
            return array();
        }
        $map['ad_id'] = $id;
        $data = $this->where($map)->find();
        return $data;
    }

    /**
     * 编辑广告位操作
     * @param integer $id 广告位ID
     * @param array $data 广告位相关数据
     * @return boolean 是否编辑成功
     */
    public function doEditAdSpace($id, $data)
    {
        if(empty($id)) {
            return false;
        }
        $map['ad_id'] = $id;
        $res = $this->where($map)->save($data);

        return (boolean)$res;
    }

    /**
     * 移动广告位操作
     * @param integer $id 广告位ID - A
     * @param integer $baseId 广告位ID - B
     * @return boolean 是否移动成功
     */
    public function doMvAdSpace($id, $baseId)
    {
        $map['ad_id'] = array('IN', array($id, $baseId));
        $order = $this->where($map)->getHashList('ad_id', 'display_order');
        if(count($order) < 2) {
            return false;
        }
        $this->where('`ad_id`='.$id)->setField('display_order', $order[$baseId]);
        $this->where('`ad_id`='.$baseId)->setField('display_order', $order[$id]);

        return true;
    }

    /**
     * 通过位置ID获取相应的广告信息
     * @param integer $place 位置ID
     * @return array 位置ID获取相应的广告信息
     */
    public function getAdSpaceByPlace($place)
    {
        if(empty($place)) {
            return array();
        }
        // 获取信息
        $map['place'] = $place;
        $map['is_active'] = 1;
        $data = $this->where($map)->order('display_order DESC')->findAll();

        return $data;
    }
}