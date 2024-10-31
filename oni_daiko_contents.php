<?php
function get_oni_daiko_post( $args = '' ) {
	global $wpdb;
    $defaults = array(
        'title' => __('New Post','oni_daiko'),
        'limit' => '10',
        'contents' => '0',
        'characters' => '100',
        'case' => 'page',
    );
    $r = wp_parse_args($args, $defaults);
    $get_title = $r['title'];
    $limit = $r['limit'];
    $get_contents = $r['contents'];
    $characters = $r['characters'];
    $case = $r['case'];
	$date_format = get_option('date_format');
	$feed_url = get_oni_daiko_plugin_uri().'oni_daiko_feed.php';
	$query = "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' ";
	$set_blog_list = $wpdb->get_results( $query, ARRAY_A );
	foreach ($set_blog_list as $oni_daiko_updated) {
		$blog_list = $oni_daiko_updated['blog_id'];
		$blog_public = $oni_daiko_updated['public'];
		$wpdb->set_blog_id($blog_list);
		if ($blog_list != 1 && $blog_public == 1 ) {
			$posts = get_posts('numberposts='.$limit);
			$blog_id = $blog_list;
			foreach($posts as $post) {
				$post_id = $post->ID;
				$get_post_time = $post->post_date;
				$unix_time = strtotime($get_post_time);
				$post_time = date($date_format,$unix_time);
				$post_author = get_the_author();
				$post_title = $post->post_title;
				$post_content = $post->post_content;
				$post_excerpt = $post->post_excerpt;
				$post_short_content = oni_daiko_short_contents($post_content, $characters);
				$oni_daiko_array = array("blog_id" => $blog_id, "ID" => $post_id, "unix_time" => $unix_time, "post_time" => $post_time, "post_author" => $post_author, "post_title" => $post_title, "post_content" => $post_content, "post_excerpt" => $post_excerpt, "post_short_content" => $post_short_content);
				$post_arr[$unix_time] = $oni_daiko_array;
			}
		}
	}
	if ( $post_arr ) {
		krsort($post_arr);
		$post_items = array_slice($post_arr, 0, $limit);
		switch ($case) {
				case 'page':
					$output = '<div class="oni_daiko_post">'."\n";
					$output .= '<h2>'.$get_title.'<span><a href="'.$feed_url.'">'.__('[RSS]','oni_daiko').'</a></span></h2>'."\n";
					foreach ($post_items as $post_item) {
						$blog_name = get_blog_option($post_item['blog_id'],'blogname');
						$blog_link = get_blog_option($post_item['blog_id'],'siteurl');
						$get_permalink = get_blog_permalink($post_item['blog_id'], $post_item['ID']);
						$content = strip_tags($post_item['post_content']);
						$short_content = $post_item['post_short_content'];
						$output .= '<h3><a href="'.$get_permalink.'" title="'.sprintf(__('Permanent Link to %s','oni_daiko'), $post_item['post_title']).'">'.$post_item['post_title'].'</a></h3>'."\n";
						$output .= '<p class="data">'.$post_item['post_time'].'</p>'."\n";
						if($get_contents == 0) {
							if($post_item['post_excerpt']){
								$output .= '<p>'."\n";
								$output .= $post_item['post_excerpt'];
								$output .= '</p>'."\n";
								$output .= '<p class="go_more">'."\n";
								$output .= '<a href="'.$get_permalink.'" title="'.sprintf(__('Permanent Link to %s','oni_daiko'), $post_item['post_title']).'">';
								$output .= __('Read the rest of this entry &nbsp;&raquo;', 'oni_daiko');
								$output .= '</a>';
								$output .= '</p>'."\n";
							} else {
								if($content > $short_content){
									$output .= '<p>'."\n";
									$output .= $short_content.'...'."\n";
									$output .= '</p>'."\n";
									$output .= '<p class="go_more">'."\n";
									$output .= '<a href="'.$get_permalink.'" title="'.sprintf(__('Permanent Link to %s','oni_daiko'), $post_item['post_title']).'">';
									$output .= __('Read the rest of this entry &nbsp;&raquo;', 'oni_daiko');
									$output .= '</a>';
									$output .= '</p>'."\n";
								} else {
									$output .= '<p>'."\n";
									$output .= $short_content."\n";
									$output .= '</p>'."\n";
								}
							}
						}
						$output .= '<p class="blog_name">(<a href="'.$blog_link.'" title="'.sprintf(__('Permanent Link to %s','oni_daiko'), $blog_name).'">'.$blog_name.'</a>)</p>'."\n";
					}
					$output .= '</div>'."\n";
					break;
				case 'feed':
					foreach ($post_items as $post_item) {
						$get_permalink = get_blog_permalink($post_item['blog_id'], $post_item['ID']);
						$output .= '<item>'."\n";
						$output .= "\t\t\t".'<title>'.$post_item['post_title'].'</title>'."\n";
						$output .= "\t\t\t".'<link>'.$get_permalink.'</link>'."\n";
						$output .= "\t\t\t".'<comments>'.$post_item['post_permalink'].'#comments</comments>'."\n";
						$output .= "\t\t\t".'<pubDate>'.$post_item['post_time'].'</pubDate>'."\n";
						$output .= "\t\t\t".'<dc:creator>'.$post_item['post_author'].'</dc:creator>'."\n";
						$output .= "\t\t\t".'<guid isPermaLink="false">'.$post_item['post_guid'].'</guid>'."\n";
						$output .= "\t\t\t".'<description><![CDATA['.$post_item['post_content'].']]></description>'."\n";
						$output .= "\t\t\t".'<content:encoded><![CDATA['.$post_item['post_content'].']]></content:encoded>'."\n";
						$output .= "\t\t\t".'<wfw:commentRss>'.$post_item['post_permalink'].'feed/</wfw:commentRss>'."\n";
						$output .= "\t\t\t".'<slash:comments>'.$post_item['post_comment_count'].'</slash:comments>'."\n";
						$output .= "\t\t\t".'</item>'."\n";
					}
					break;
		}
		return $output;
	}
}