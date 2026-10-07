<?php defined('ABSPATH') || exit; get_header(); ?>
<main id="main">
<?php
if(is_front_page()) { fbs_home(); }
elseif(is_home()||is_category()||is_tag()) { fbs_journal(); }
elseif(is_page('gallery')) { fbs_gallery(); }
elseif(is_page('services')) {
    echo '<div class="wrap"><div class="page-heading"><span class="eyebrow">Our services</span><h1>Good advice.<br><em>Careful fitting.</em></h1><p>Consultation, measuring and professional fitting for homes and businesses.</p></div>'.fbs_service_cards().'</div><div style="height:80px"></div>';
}
elseif(is_404()) { echo '<div class="wrap section"><h1>Let’s find your next step.</h1><div class="actions"><a class="button" href="'.esc_url(home_url('/services/')).'">Explore services ↗</a></div></div>'; }
elseif(have_posts()) {
    while(have_posts()) { the_post();
        if(is_singular(array('post','fbs_service'))||get_post_meta(get_the_ID(),'_fbs_visual_page',true)) { fbs_native_content(get_post()); }
        else { echo do_shortcode(fbs_rewrite(apply_filters('the_content',get_the_content()))); }
    }
}
else { echo '<div class="wrap section"><h1>FBS Flooring services</h1><p>Use FBS Website in your dashboard to import the website content.</p></div>'; }
?>
</main>
<?php get_footer(); ?>
