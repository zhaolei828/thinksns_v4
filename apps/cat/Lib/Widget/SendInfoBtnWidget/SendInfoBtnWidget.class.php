<?php

class SendInfoBtnWidget extends Widget
{
    public function render($data)
    {
        if(!$data['entity']['can_rec'])
        {
            return '';
        }
        $data['mid'] = $this->mid;
        if($data['entity']['rec_entity']==0)
        {
            $data['send_entitys']=D('cat_entity')->select();


        }
        else{
            $data['send_entitys']=D('cat_entity')->where('entity_id in ('.$data['entity']['rec_entity'].')')->select();
        }

        $data['first_entity_info']=D('Info')->getList('entity_id='.$data['send_entitys'][0]['entity_id'].' and uid='.$this->mid,8);
        $content=$this->renderFile(dirname(__FILE__).'/tpl.html',$data);

        return $content;
    }
}