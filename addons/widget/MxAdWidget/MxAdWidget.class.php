<?php
/**
 * 
 */
class MxAdWidget extends Widget
{
	public function render($data) {
        // Create place if not exists
        $var['width']=isset($data['width'])?$data['width']:180;
        $var['height']=isset($data['height'])?$data['height']:180;
        $name = $data['name'];
        $description = $data['description'];

        if(!D('MxAdPlace')->isPlaceExist($name)) {
            D('MxAdPlace')->addPlace($name, $description);
        }

        // Get ads
        $ads = D('MxAd')->getAdSpaceListAtPlace($name);
        $var['ads'] = $ads;
        // 渲染模版
        $template = 'mxadlist';
        $content = $this->renderFile(dirname(__FILE__)."/".$template.".html", $var);
        // 输出数据
        return $content;
    }
}