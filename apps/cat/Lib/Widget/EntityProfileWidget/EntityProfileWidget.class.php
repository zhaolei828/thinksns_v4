<?php

/**实体单属性输出部件
 * Class EntityProfileWidget
 */
class EntityProfileWidget extends Widget{
    public function render($data){
        if(isset($data['name']))
        {
            $map['name']=$data['name'];
        }
        if(isset($data['entity_id']))
        {
            $map['entity_id']=$data['entity_id'];
        }
        $entitys=D('cat_entity')->where($map)->limit(1)->select();
        $entity=$entitys[0];
        return $entity[$data['p_name']];
    }
}