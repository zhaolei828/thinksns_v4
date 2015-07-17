<?php
/**
 * 
 */
class MxSubAdWidget extends Widget
{
	/**
	 * 模板渲染
	 * @param array $data 相关数据
	 * @return string 用户身份选择模板
	 */
	public function render($data) {
        // Load ad
        $var['width']=isset($data['width'])?$data['width']:180;
        $var['height']=isset($data['height'])?$data['height']:180;
        $ad = $data['ad'];
        switch($ad['display_type']) {
            case 1:
                $template = 'html_ad';
                $var['content'] = $ad['content_html'];
                break;
            case 2:
                $template = 'html_ad';
                $var['ad'] = $ad;
                $var['content'] = $ad['content_code'];
                break;
            case 3:
                $template = 'pic_ad';
                $var['ad'] = $ad;
                for($i=0;$i<6;$i++) {
                    $picture = $ad["content_picture$i"];
                    $link = $ad["content_link$i"];
                    if(intval($picture)) {
                        $picture = getImageUrlByAttachId($picture);
                        $var['ad']['pictures'][] = array('picture'=>$picture, 'link'=>$link);
                    }
                }
                break;
        }
        // Render
        $content = $this->renderFile(dirname(__FILE__)."/".$template.".html", $var);
        return $content;
    }
}