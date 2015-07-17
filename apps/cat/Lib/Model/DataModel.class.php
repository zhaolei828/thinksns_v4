<?php
/**字段数据模型，通过该模型可以方便地对字段信息实现读和取
 * Class DataModel
 */
class DataModel extends Model
{
    protected $tableName = "cat_data";

    public function addData($name, $value, $info_id, $entity_id)
    {
        $map['name'] = $name;
        $map['entity_id'] = $entity_id;
        $profile = D('cat_field')->where($map)->limit(1)->select();
        $content_data['field_id'] = $profile[0]['field_id'];
        if (!is_array($value)) {
            //如果值不是数组
            if ($profile[0]['input_type'] == IT_DATE) {
                $content_data['value'] = strtotime($value);
            } else {
                $content_data['value'] = $value;
            }
            $content_data['info_id'] = $info_id;
            return $this->add($content_data);
        } else {
            $rs = 1;
            foreach ($value as $v) {
                //如果是数组
                $content_data['value'] = $v;
                $content_data['info_id'] = $info_id;
                $rs = ($rs && $this->add($content_data));
            }
            return $rs;
        }
    }

    /**通过信息ID获取到所有相关数据
     * @param $info_id
     * @return array
     */
    public function getByInfoId($info_id)
    {
        $map['info_id'] = $info_id;
        $data = array();
        $dataRows = $this->where($map)->order('data_id asc')->select();
        foreach ($dataRows as $v) {
            $profiles = D('cat_field')->where('field_id=' . $v['field_id'])->limit(1)->select();

            $profile = $profiles[0];
            $data[$profile['name']]['data'][] = $v['value'];
            $data[$profile['name']]['field'] = $profile;
            $data[$profile['name']]['values'] = $values = json_decode($profile['option'], true);
        }
        return $data;
    }
}