<?php
defined('ABSPATH') || exit;
require_once __DIR__.'/setup.php';
function fbs_seed($name) {
    static $cache = array();
    if (!isset($cache[$name])) { $cache[$name] = json_decode(file_get_contents(__DIR__.'/seed/'.$name.'.json'), true) ?: array(); }
    return $cache[$name];
}
function fbs_settings() {
    $seed=fbs_seed('site');
    return wp_parse_args(get_option('fbs_settings',array()), array('headline'=>$seed['headline'],'intro'=>$seed['intro'],'phone'=>$seed['phone'],'email'=>$seed['email'],'address'=>$seed['address'],'announcement'=>$seed['announcement'],'hero_id'=>0));
}
add_action('after_setup_theme',function(){
    add_theme_support('title-tag');add_theme_support('post-thumbnails');add_theme_support('responsive-embeds');
    add_theme_support('html5',array('search-form','gallery','caption','style','script'));
    add_theme_support('editor-styles');add_editor_style('assets/editor.css');
    register_nav_menus(array('primary'=>'Main navigation'));
});
add_action('init',function(){
    register_post_type('fbs_service',array('labels'=>array('name'=>'Services','singular_name'=>'Service','add_new_item'=>'Add a service','edit_item'=>'Edit service'),'public'=>true,'show_in_rest'=>true,'supports'=>array('title','editor','excerpt','thumbnail','page-attributes'),'menu_icon'=>'dashicons-hammer','rewrite'=>array('slug'=>'services','with_front'=>false),'has_archive'=>false));
    register_post_type('fbs_inspiration',array('labels'=>array('name'=>'Inspiration photos','singular_name'=>'Inspiration photo','add_new_item'=>'Add inspiration photo'),'public'=>false,'show_ui'=>true,'show_in_rest'=>true,'supports'=>array('title','editor','thumbnail','page-attributes'),'menu_icon'=>'dashicons-format-gallery'));
});
add_action('wp_enqueue_scripts',function(){
    wp_enqueue_style('fbs-site',get_template_directory_uri().'/assets/site.css',array(),'2.1.0');
    wp_enqueue_script('fbs-site',get_template_directory_uri().'/assets/site.js',array(),'2.1.0',true);
    wp_enqueue_script('fbs-motion',get_template_directory_uri().'/assets/motion.js',array('fbs-site'),'2.1.0',true);
    wp_add_inline_script('fbs-site','window.FBSContact='.wp_json_encode(fbs_settings(),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT).';','before');
});
add_action('wp_head',function(){
    $desc=is_singular()?get_the_excerpt():fbs_settings()['intro'];
    echo '<meta name="description" content="'.esc_attr(wp_strip_all_tags($desc)).'">';
    echo '<script>document.addEventListener("error",function(e){var im=e.target;if(im.tagName==="IMG"&&im.dataset.fallback&&!im.dataset.retried){im.dataset.retried="true";im.src=im.dataset.fallback;}},true);</script>';
    echo '<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 32 32\'%3E%3Crect width=\'32\' height=\'32\' rx=\'5\' fill=\'%2326362e\'/%3E%3Cpath d=\'M8 23V9h15M8 15h11\' stroke=\'%23dbb77c\' stroke-width=\'3\' fill=\'none\'/%3E%3C/svg%3E">';
});
function fbs_rewrite($html) {
    $s=fbs_settings();$seed=fbs_seed('site');
    $html=str_replace(array($seed['address'],$seed['intro'],$seed['announcement']),array(esc_html($s['address']),esc_html($s['intro']),esc_html($s['announcement'])),$html);
    $html=str_replace(array('info@fbsflooring.ie','+353 83 044 1400','+353830441400','353830441400'),array(esc_html($s['email']),esc_html($s['phone']),preg_replace('/[^+0-9]/','',$s['phone']),preg_replace('/\D/','',$s['phone'])),$html);
    return preg_replace_callback('~(href|src|data-gallery)="(/[^"<>]*)"~',function($m){$url=strpos($m[2],'/assets/')===0?get_template_directory_uri().$m[2]:home_url($m[2]);return $m[1].'="'.esc_url($url).'"';},$html);
}
function fbs_chrome($part) {
    $html=file_get_contents(__DIR__.'/seed/'.$part.'.html');
    if($part==='header'&&has_nav_menu('primary')){
        $menu=wp_nav_menu(array('theme_location'=>'primary','container'=>false,'echo'=>false,'items_wrap'=>'%3$s','depth'=>1));
        $html=preg_replace('~<nav class="nav".*?</nav>~s','<nav class="nav" aria-label="Main navigation"><ul class="fbs-menu">'.$menu.'</ul></nav>',$html,1);
        $html=preg_replace('~<nav id="mobile-menu".*?</nav>~s','<nav id="mobile-menu" class="mobile-menu" aria-label="Mobile navigation" hidden><ul style="list-style:none;margin:0;padding:0">'.$menu.'</ul></nav>',$html,1);
    }
    return fbs_rewrite($html);
}
function fbs_service_cards() {
    ob_start();$posts=get_posts(array('post_type'=>'fbs_service','numberposts'=>-1,'orderby'=>'menu_order','order'=>'ASC','post_status'=>'publish'));
    echo '<div class="cards" data-service-cards>';
    foreach($posts as $i=>$p){echo '<article class="service-card"><span class="number">'.esc_html(sprintf('%02d',$i+1)).'</span><h3>'.esc_html(get_the_title($p)).'</h3><p>'.esc_html(get_the_excerpt($p)).'</p><a class="text-link" href="'.esc_url(get_permalink($p)).'">Explore the service ↗</a></article>';}
    echo '</div>';return ob_get_clean();
}
function fbs_post_card($p) {
    $id=$p->ID;$terms=get_the_category($id);$topic=$terms?$terms[0]->name:'Advice';
    echo '<article class="article-card" data-article data-title="'.esc_attr(strtolower(get_the_title($id))).'" data-category="'.esc_attr($topic).'"><a class="card-photo" href="'.esc_url(get_permalink($id)).'">';
    if(has_post_thumbnail($id)){echo get_the_post_thumbnail($id,'large');}
    echo '</a><span class="meta">'.esc_html($topic).'</span><h3><a href="'.esc_url(get_permalink($id)).'">'.esc_html(get_the_title($id)).'</a></h3><p>'.esc_html(get_the_excerpt($id)).'</p><a class="text-link" href="'.esc_url(get_permalink($id)).'">Read the guide ↗</a></article>';
}
function fbs_home() {
    $html=file_get_contents(__DIR__.'/seed/home.html');$s=fbs_settings();
    $html=str_replace('Expert fitting.<br>A home that feels<br><em>finished.</em>',nl2br(esc_html($s['headline'])),$html);
    if($s['hero_id']){$url=wp_get_attachment_image_url($s['hero_id'],'full');if($url){$html=preg_replace('~(<div class="hero-visual"><img src=")[^"]+~','$1'.esc_url($url),$html,1);}}
    $html=preg_replace('~<div class="cards" data-service-cards>.*?</div></div></section>~s',fbs_service_cards().'</div></section>',$html,1);
    ob_start();foreach(get_posts(array('numberposts'=>3,'post_status'=>'publish')) as $p){fbs_post_card($p);} $cards=ob_get_clean();
    $html=preg_replace('~(<div class="cards" data-guide-cards>).*?(</div></div></section>)~s','$1'.$cards.'$2',$html,1);
    echo fbs_rewrite($html);
}
function fbs_journal() {
    $posts=get_posts(array('numberposts'=>-1,'post_status'=>'publish','category'=>is_category()?get_queried_object_id():0));$topics=array();
    foreach($posts as $p){$t=get_the_category($p->ID);if($t){$topics[]=$t[0]->name;}}$topics=array_unique($topics);sort($topics);
    echo '<div class="wrap"><div class="page-heading"><span class="eyebrow">The FBS journal</span><h1>Good preparation.<br><em>Better conversations.</em></h1><p>Practical guidance for planning a consultation and preparing for fitting.</p></div><div class="blog-tools"><label class="field">Search guides<input id="blog-search" type="search" placeholder="Try measurements or fitting"></label><label class="field">Topic<select id="blog-category"><option value="">All topics</option>';
    foreach($topics as $t){echo '<option>'.esc_html($t).'</option>';}
    echo '</select></label><p id="blog-count" aria-live="polite">'.count($posts).' guides</p></div><div class="cards blog-grid">';foreach($posts as $p){fbs_post_card($p);}echo '</div><div id="blog-empty" hidden><h2>No matching guides.</h2><button type="button" id="blog-reset" class="text-button">Clear filters</button></div></div><div style="height:80px"></div>';
}
function fbs_gallery() {
    $photos=get_posts(array('post_type'=>'fbs_inspiration','numberposts'=>-1,'orderby'=>'menu_order','order'=>'ASC'));
    echo '<div class="wrap"><div class="page-heading"><span class="eyebrow">Interior inspiration</span><h1>A feeling for<br><em>your space.</em></h1><p>Look at the light, pattern and way a floor connects a room. Bring your ideas to FBS.</p></div><div class="gallery-grid">';
    foreach($photos as $p){$url=get_the_post_thumbnail_url($p,'full');if(!$url){continue;}echo '<button class="gallery-item" data-gallery="'.esc_url($url).'" data-caption="'.esc_attr(get_the_title($p)).'" aria-label="Open '.esc_attr(get_the_title($p)).' image"><div class="gallery-photo">'.get_the_post_thumbnail($p,'large').'</div><span>'.esc_html(get_the_title($p)).' ↗</span></button>';}
    echo '</div><p class="media-note">Illustrative interiors shown for inspiration. These are not presented as completed FBS jobs.</p></div><dialog class="lightbox" id="gallery-dialog" aria-labelledby="gallery-caption"><div class="lightbox-header"><span id="gallery-caption"></span><button class="icon-button" id="gallery-close" type="button" aria-label="Close image">×</button></div><img id="gallery-image" alt=""></dialog><div style="height:60px"></div>';
}
function fbs_native_content($p) {
    echo '<article class="article"><span class="eyebrow">'.(get_post_type($p)==='fbs_service'?'FBS services':'FBS advice').'</span><h1>'.esc_html(get_the_title($p)).'</h1>';
    if($p->post_excerpt){echo '<p class="lead">'.esc_html($p->post_excerpt).'</p>';}
    if(has_post_thumbnail($p)){echo '<div class="article-photo">'.get_the_post_thumbnail($p,'large').'</div>';}
    echo apply_filters('the_content',$p->post_content);
    echo '<div class="callout"><h2>Make it about your space.</h2><p>Discuss your consultation or fitting requirements with FBS.</p><div class="actions"><a class="button" href="'.esc_url(home_url('/contact-us/#quote')).'">Talk to FBS ↗</a></div></div></article>';
}
add_action('admin_menu',function(){add_menu_page('FBS Website','FBS Website','manage_options','fbs-website','fbs_admin_page','dashicons-admin-home',3);});
add_action('admin_init',function(){register_setting('fbs_settings_group','fbs_settings',array('sanitize_callback'=>function($input){$r=array();foreach(array('headline','intro','phone','address','announcement') as $k){$r[$k]=sanitize_textarea_field($input[$k]??'');}$r['email']=sanitize_email($input['email']??'');$r['hero_id']=absint($input['hero_id']??0);return $r;}));});
add_action('admin_enqueue_scripts',function($hook){if($hook!=='toplevel_page_fbs-website'){return;}wp_enqueue_media();wp_enqueue_script('fbs-admin',get_template_directory_uri().'/assets/admin.js',array('jquery'),'2.0.0',true);});
function fbs_admin_page(){
    if(!current_user_can('manage_options')){return;}$s=fbs_settings();echo '<div class="wrap"><h1>FBS Website</h1><p>Change your homepage, contact details and photos here. Services and advice use the visual WordPress editor.</p><p><a class="button" href="'.esc_url(admin_url('edit.php?post_type=fbs_service')).'">Edit services</a> <a class="button" href="'.esc_url(admin_url('edit.php')).'">Edit advice articles</a> <a class="button" href="'.esc_url(admin_url('edit.php?post_type=fbs_inspiration')).'">Edit inspiration photos</a> <a class="button" href="'.esc_url(admin_url('upload.php')).'">Upload photos</a> <a class="button" href="'.esc_url(home_url('/')).'">View website</a></p>';
    settings_errors();echo '<form method="post" action="options.php">';settings_fields('fbs_settings_group');echo '<table class="form-table">';
    foreach(array('headline'=>'Homepage headline','intro'=>'Homepage introduction','announcement'=>'Top notice','phone'=>'Business phone','email'=>'Business email','address'=>'Business address') as $key=>$label){echo '<tr><th><label for="fbs-'.$key.'">'.$label.'</label></th><td><textarea class="large-text" rows="'.($key==='headline'?3:2).'" id="fbs-'.$key.'" name="fbs_settings['.$key.']">'.esc_textarea($s[$key]).'</textarea></td></tr>';}
    echo '<tr><th>Homepage photo</th><td><input id="fbs-hero-id" name="fbs_settings[hero_id]" type="hidden" value="'.esc_attr($s['hero_id']).'"><button type="button" class="button" id="fbs-choose-hero">Choose photo</button> <button type="button" class="button" id="fbs-reset-hero">Use original photo</button><div id="fbs-hero-preview">';if($s['hero_id']){echo wp_get_attachment_image($s['hero_id'],'medium');}echo '</div></td></tr></table>';submit_button('Save website changes');echo '</form><hr><h2>First-time setup</h2><p>Use this on a staging copy or a new WordPress installation. Imports services, guidance and pictures. Existing same-slug content is left unchanged; existing products are not deleted.</p><p>Changing an existing homepage is a separate choice. Review the new pages before switching it.</p><button type="button" class="button button-primary" id="fbs-import-content">Import the service website</button><p id="fbs-import-status" role="status"></p><script>window.FBSSetup='.wp_json_encode(array('ajax'=>admin_url('admin-ajax.php'),'nonce'=>wp_create_nonce('fbs_setup'),'total'=>fbs_import_total())).';</script></div>';
}
