<?php
defined('ABSPATH') || exit;
function fbs_blocks($sections){$html='';foreach($sections as $section){$html.='<!-- wp:heading --><h2 class="wp-block-heading">'.esc_html($section[0]).'</h2><!-- /wp:heading --><!-- wp:paragraph --><p>'.esc_html($section[1]).'</p><!-- /wp:paragraph -->';}return $html;}
function fbs_import_items(){
    $items=array();$site=json_decode(file_get_contents(__DIR__.'/seed/site.json'),true);$guides=json_decode(file_get_contents(__DIR__.'/seed/guides.json'),true);$pages=json_decode(file_get_contents(__DIR__.'/seed/pages.json'),true);
    foreach($site['services'] as $s){$items[]=array('kind'=>'fbs_service','slug'=>$s['slug'],'title'=>$s['title'],'excerpt'=>$s['summary'],'image'=>$s['image'],'content'=>fbs_blocks(array_merge(array(array('Start with your space',$s['intro'])),$s['steps'],array(array('Before fitting is agreed',$s['note'])))));}
    foreach($guides as $g){$items[]=array('kind'=>'post','slug'=>$g['slug'],'title'=>$g['title'],'excerpt'=>$g['summary'],'image'=>$g['image'],'category'=>$g['category'],'content'=>fbs_blocks($g['sections']));}
    foreach($pages as $p){$items[]=array('kind'=>'page','slug'=>$p['slug'],'title'=>$p['title'],'excerpt'=>$p['description'],'content'=>$p['content'],'visual'=>$p['visual']??false);}
    foreach(array('hero'=>'Pattern & warmth','kitchen'=>'Light & texture','home'=>'A calm, open room','consultation'=>'A connected living space') as $key=>$title){$items[]=array('kind'=>'fbs_inspiration','slug'=>'inspiration-'.$key,'title'=>$title,'image'=>$key,'content'=>fbs_blocks(array(array('Interior inspiration','Illustrative imagery from the FBS website, shown for inspiration. This is not presented as completed FBS work.'))));}
    return $items;
}
function fbs_import_total(){return count(fbs_import_items());}
function fbs_import_image($key){
    $file=__DIR__.'/assets/'.$key.'.webp';if(!is_file($file)){return 0;}
    $existing=get_posts(array('post_type'=>'attachment','post_status'=>'inherit','numberposts'=>1,'meta_key'=>'_fbs_image_key','meta_value'=>$key));if($existing){return $existing[0]->ID;}
    require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
    $tmp=wp_tempnam($file);if(!$tmp||!copy($file,$tmp)){return 0;}
    $id=media_handle_sideload(array('name'=>$key.'.webp','tmp_name'=>$tmp),0,'FBS website illustration');
    if(is_wp_error($id)){@unlink($tmp);return 0;}update_post_meta($id,'_fbs_image_key',$key);update_post_meta($id,'_wp_attachment_image_alt','Illustrative flooring imagery from the FBS website');return $id;
}
add_action('wp_ajax_fbs_import_content',function(){
    if(!current_user_can('manage_options')){wp_send_json_error(array('message'=>'Administrator access is required.'),403);}check_ajax_referer('fbs_setup','nonce');
    $index=absint($_POST['index']??0);$items=fbs_import_items();if(!isset($items[$index])){flush_rewrite_rules(false);wp_send_json_success(array('done'=>true,'message'=>'Import complete. Review the imported pages and choose your homepage in Settings → Reading.'));}
    $item=$items[$index];$existing=get_page_by_path($item['slug'],OBJECT,$item['kind']);
    if($existing){wp_send_json_success(array('done'=>false,'message'=>'Kept existing: '.$item['title']));}
    $id=wp_insert_post(array('post_type'=>$item['kind'],'post_status'=>'publish','post_title'=>$item['title'],'post_name'=>$item['slug'],'post_excerpt'=>$item['excerpt']??'','post_content'=>$item['content'],'menu_order'=>$index),true);
    if(is_wp_error($id)){wp_send_json_error(array('message'=>$id->get_error_message()),500);}
    if(!empty($item['image'])){$image=fbs_import_image($item['image']);if($image){set_post_thumbnail($id,$image);}}
    if(!empty($item['category'])){$term=term_exists($item['category'],'category');if(!$term){$term=wp_insert_term($item['category'],'category');}if(!is_wp_error($term)){wp_set_post_categories($id,array((int)$term['term_id']));}}
    if($item['kind']==='page'){
        if(!empty($item['visual'])){update_post_meta($id,'_fbs_visual_page',1);}
        if($item['slug']==='home'&&!get_option('page_on_front')){update_option('show_on_front','page');update_option('page_on_front',$id);}
        if($item['slug']==='blog'&&!get_option('page_for_posts')){update_option('page_for_posts',$id);}
    }
    wp_send_json_success(array('done'=>false,'message'=>'Imported: '.$item['title']));
});
