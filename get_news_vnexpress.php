<?php
/*
Plugin Name: Get News VNexpress.Net
Plugin URI: https://huykira.net/webmaster/wordpress/plugin-lay-tin-tu-dong-tu-vnexpress-net.html
Description: Plugin get News VNexpress.Net by Huy Kira
Author: Huy Kira
Version: 1.0
Author URI: http://www.huykira.net
*/
if ( !function_exists( 'add_action' ) ) {
  echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
  exit;
}

define('HK_VNEXPRESS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('HK_VNEXPRESS_PLUGIN_RIR', plugin_dir_path(__FILE__));
require_once( ABSPATH . "wp-includes/pluggable.php" );
if (!class_exists('simple_html_dom_node')) {
	require_once(HK_VNEXPRESS_PLUGIN_RIR . 'includes/simple_html_dom.php');
}
	add_action('admin_menu', 'gnv_add_menu_hk');
	function gnv_add_menu_hk() {
		add_menu_page( 
        	__( 'Get News Vnexpress', 'textdomain' ),
		        'News Vnexpress',
		        'manage_options',
		        'hk_news_vnexppress',
		        'gnv_create_page',
		        'dashicons-index-card'
			);
		add_action( 'admin_init', 'gnv_register_mysettings' );
    };
    function gnv_register_mysettings() {
		register_setting( 'my-settings-group', 'gnv_add_menu_hk' );
		register_setting( 'my-settings-group', 'some_other_option' );
		register_setting( 'my-settings-group', 'option_etc' );
	}

    function gnv_custom_style() {
	   wp_enqueue_style( 'boots_css', HK_VNEXPRESS_PLUGIN_URL.'scripts/css/bootstrap.min.css', false, '1.0.0' );
	   wp_enqueue_style( 'custom_css', HK_VNEXPRESS_PLUGIN_URL.'scripts/css/style.css', false, '1.0.0' );
	   wp_enqueue_script( 'boots_js', HK_VNEXPRESS_PLUGIN_URL.'scripts/js/bootstrap.min.js', true, '1.0.0' );
	   wp_enqueue_script( 'custom_js', HK_VNEXPRESS_PLUGIN_URL.'scripts/js/custom.js', true, '1.0.0' );
	}
	if(gnv_curPageURL()==admin_url('admin.php?page=hk_news_vnexppress')) {
	add_action( 'admin_enqueue_scripts', 'gnv_custom_style' );
	}
	if(gnv_curPageURL()==admin_url('admin.php?page=hk_news_vnexppress&settings-updated=true')) {
	add_action( 'admin_enqueue_scripts', 'gnv_custom_style' );
	}

	function gnv_create_page() { ?>
		<?php $options = get_option( 'gnv_add_menu_hk' ); ?>

    	<div class="wrap tp-app">
		    <h2>Lấy bài viết từ VNEXPRESS.NET</h2>
		    <br>
    		<div class="col-xs-12 col-sm-12 col-md-6">
				<form name="post" action="" method="post" id="post" autocomplete="off">
					<?php wp_nonce_field('get_new_express'); ?>
					<div class="row">
						<div class="input-muti">  
							<div class="form-group">
								<label for="link">Nhập link bài viết</label>
								<input required="required" name="link[]" type="url" class="form-control" id="" placeholder="Nhập link vào đây" value="<?php if(isset($_POST['link'])) { echo sanitize_text_field($_POST['link'][0]); } ?>">
								<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
							</div>
						</div>
						<div class="list-input">
                            <?php if(isset($_POST['add_post']) and ($_POST['add_post'] == 'true')){
                            	$links = $_POST['link'];
                            	if (is_array($links)) {
						            foreach ($links as &$link) {
						                $link = sanitize_text_field($link);
						            }
	                                foreach ($links as $key => $value) { if ($key==0) {} else { ?>
	                                <div class="form-group">
	                                    <label for="link">Nhập link</label>
	                                    <input required="required" name="link[]" type="url" class="form-control" value="<?php echo $value; ?>">
	                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                </div>       
                            <?php } } } } ?>
                        </div>
                        <div class="more">
                            <span class="click-more">Thêm link</span>
                        </div>
                        <div class="row">
                        	<div class="col-xs-12 col-sm-12 col-md-6">
                        		<div class="form-group">
		                            <label for="cat">Chọn chuyên mục</label>
		                            <select name="cat" id="input" class="form-control" required="required">
		                                <?php $args = array( 
		                                    'hide_empty' => 0,
		                                    'taxonomy' => 'category',
		                                    ); 
		                                    $cates = get_categories( $args ); 
		                                    foreach ( $cates as $cate ) { 
		                                    	if (isset($_POST['cat'])){
		                                    		$cat_id = sanitize_text_field($_POST['cat']); 
		                                    	} 
		                                ?>
		                                        <option value="<?php echo $cate->term_id; ?>" <?php if($cat_id == $cate->term_id) {echo 'selected'; } ?>><?php echo $cate->name; ?></option>
		                                <?php } ?>
		                                <!-- Get category -->
		                            </select>
		                        </div>
                        	</div>
                        	<div class="col-xs-12 col-sm-12 col-md-6">
                        		<div class="form-group">
		                            <label for="cat">Chọn trạng thái</label>
		                            <select name="status" id="input" class="form-control" required="required">
		                                <option value="Pending" <?php if(sanitize_text_field($_POST['status']) == 'Pending') {echo 'selected'; } ?>>Xét duyệt</option>
		                                <option value="Publish" <?php if(sanitize_text_field($_POST['status']) == 'Publish') {echo 'selected'; } ?>>Đăng luôn</option>
		                            </select>
		                        </div>
                        	</div>
                        </div>
                        <input type="hidden" name="add_post" value="true">
						<div class="alignleft">
				            <button>Nhập bài viết</button>
				        </div>
				        <?php 
					    	if(isset($_POST['add_post']) and ($_POST['add_post'] == 'true')){
							    if(isset($_POST['cat'])){
							    	$cat = sanitize_text_field($_POST['cat']);
							    	if(isset($_POST['status'])){
								    	$status = sanitize_text_field($_POST['status']);
								    	if(isset($_POST['link'])){
									    	$links = $_POST['link'];
									    	if (is_array($links)) {
									            foreach ($links as &$link) {
									                $link = sanitize_text_field($link);
									            }
									            foreach ($links as $key => $value) {
											       	qnv_get_express($value,$cat,$status);
											    }
									        }
									    }
								    }
								    
							    }
							}
					    ?>
					</div>
				</form>
				<div class="row">
					<div class="info-setting">
						<form action="options.php" method="POST" role="form">
			    			<?php settings_fields( 'my-settings-group' ); ?>
			    			<?php do_settings_sections( 'my-settings-group' ); ?>
							<p><input type="checkbox" id="gnv_add_menu_hk[cmt]" name="gnv_add_menu_hk[cmt]" value="1" <?php if(isset($options['cmt'])) {echo 'checked';} ?>/> <label for="gnv_add_menu_hk[cmt]">Bật chức năng thảo luận</label></p>
							<p class="kiki">Loại bỏ nội dung lấy về bằng cách thêm các <strong>vùng chọn (class, id...)</strong> vào các textbox bên dưới! </p>
							<div class="row">
								<div class="option-list">
									<div class="col-xs-12 col-sm-12 col-md-6">
										<div class="input-hk form-group">
											<input type="text" class="form-control" name="gnv_add_menu_hk[list-op][]" value="<?php if(isset($options['list-op'][0])) {echo $options['list-op'][0];} ?>">
											<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
										</div>
									</div>
								</div>
								<div class="list-hihi">
									<?php if(isset($options['list-op'])) { ?>
										<?php foreach ($options['list-op'] as $key => $value) { ?>
											<?php if($key==0){} else { ?>
												<div class="col-xs-12 col-sm-12 col-md-6">
													<div class="input-hk form-group">
														<input type="text" class="form-control" name="gnv_add_menu_hk[list-op][]" value="<?php echo $value; ?>">
														<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
													</div>
												</div>
											<?php } ?>
										<?php } ?>
									<?php } ?>
								</div>
								<div class="clear"></div>
								<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
									<span class="click-add">Thêm tùy chọn</span>
								</div>
							</div>
							<div class="alignleft">
					            <button>Lưu</button>
					        </div>
						</form>
						<div class="clear"></div>
					</div>
				</div>
	        </div>
	        <div class="col-xs-12 col-sm-12 col-md-6">
	        	<div class="info-plugin">
	        		<h2>Hướng dẫn sử dụng</h2>
	        		<p><strong>Bước 1: </strong>Nhập link bài viết từ VNEXPRESS.NET. Để lấy nhiều bài viết về 1 lần có thể ấn <span><strong>thêm link</strong></span> sau đó nhập link...</p>
	        		<p><strong>Bước 2: </strong>Chọn chuyên mục chứa bài viết.</p>
	        		<p><strong>Bước 3: </strong>Chọn trạng thái của bài viết là <strong>Xét duyệt</strong> hoặc <strong>Đăng bài.</strong></p>
	        		<p><strong>Bước 4: </strong>Ấn nhập "Nhập bài viết" và chờ trong giây lát, bài viết sẽ được lấy website của bạn!</p>
	        		<p><strong><i>Lưu ý</i>: </strong>Các bạn có thể loại bỏ các nội dung ko cần thiết khi lấy về bằng phần tùy chỉnh phía dưới. Hoặc có thể bật mở chức năng thảo luận!</p>
	        		<hr>
	        		<p>Plugin chỉ lấy được tin từ website vnexpress.net và chỉ lấy được bài viết dạng tin tức, hình ảnh... Plugin <strong>không</strong> lấy được video.</p>
	        		<p>Plugin đang trong thời gian phát triển, rất mong nhận được sự góp ý của các bạn!</p>
	        		<p>Plugin được share chính thức tại <a href="http://huykira.net">huykira.net</a></p>
	        	</div>
	        </div>
		</div>
		<?php if(isset($options['cmt'])) { ?>
			<div class="clear"></div>
			<div class="wrap tp-app">
			    <h2>Thảo luận về plugin</h2>
			    <br>
	        	<div class="cmt">
	        		<div class="fb-comments" data-width="100%" data-href="https://huykira.net/webmaster/wordpress/plugin-lay-tin-tu-dong-tu-vnexpress-net.html" data-numposts="3"></div>
	        	</div>
	        	<div id="fb-root"></div>
			</div>
		<?php } ?>
	<?php }
		function qnv_get_express($link,$cat,$status) {  
		    $url = $link;
		    $web = explode('/', $url)[2];
		    if(!gnv_check_link_die($url)) { ?>
				<div class="clear"></div>
				<br>
				<div class="alert alert-danger">
				    <p><?php echo $url; ?> - Link không tồn tại hoặc lỗi!</p>
				</div>
		    <?php } else if($web == 'vnexpress.net' or $web == 'kinhdoanh.vnexpress.net' or $web == 'giaitri.vnexpress.net' or $web == 'thethao.vnexpress.net' or $web == 'suckhoe.vnexpress.net' or $web == 'giadinh.vnexpress.net' or $web == 'dulich.vnexpress.net' or $web == 'sohoa.vnexpress.net' or $web == 'raovat.vnexpress.net') {
			    $html = file_get_html($url);
			    $options = get_option( 'add_menu_hk' );
			    if(isset($options['list-op'])) { 
			    	foreach ($options['list-op'] as $select) {
			    		foreach ($html->find($select) as $value) {
					        $value->outertext = '';
					    }
			    	}
			    }
			    foreach ($html->find('img') as $value) {
			        $value->outertext = '<p style="text-align:center"><img src="'.$value->src.'" >';
			    }
				foreach ($html->find('.block_timer_share') as $value) {
			        $value->outertext = '';
			    }
			    foreach ($html->find('a') as $value) {
			        $value->outertext = '<p>'.$value->innertext.'</p>';
			    } 
			    foreach ($html->find('#box_tinkhac_detail') as $value) {
			        $value->outertext = '';
			    } 
			    foreach ($html->find('#box_comment') as $value) {
			        $value->outertext = '';
			    } 
			    foreach ($html->find('.block_input_comment') as $value) {
			        $value->outertext = '';
			    } 
			    foreach ($html->find('.clear') as $value) {
			        $value->outertext = '';
			    } 
			    foreach ($html->find('.btn_icon_show_slide_show') as $value) {
			        $value->outertext = '';
			    }
			    foreach ($html->find('script') as $value) {
			        $value->outertext = '';
			    }
			    foreach ($html->find('table') as $value) {
			        $value->outertext = $value->innertext;
			    }
			    foreach ($html->find('.desc_cation') as $value) {
			        $value->outertext = $value->innertext;
			    }
			    foreach ($html->find('.short_intro') as $value) {
			        $value->outertext = $value->innertext;
			    }
			    foreach ($html->find('.block_thumb_slide_show') as $value) {
			        $value->outertext = $value->innertext;
			    }
			    foreach ($html->find('.item_slide_show') as $value) {
			        $value->outertext = $value->innertext;
			    }
			    foreach ($html->find('#social_like') as $value) {
			        $value->outertext = '';
			    }
			    foreach ($html->find('#box_tinlienquan') as $value) {
			        $value->outertext = '';
			    }
			    foreach ($html->find('.title_div_fbook') as $value) {
			        $value->outertext = '';
			    }
			    foreach ($html->find('.relative_new') as $value) {
			    	$value->outertext = '';
			    }
			    foreach ($html->find('div') as $value) {
			    	if(isset($value->attr['data-component-type'])) {
			            $value->outertext = '';
			        }
			    }
			    foreach ($html->find('input') as $value) {
			    	$value->outertext = '';
			    }
			    foreach ($html->find('#result_other_news') as $value) {
			    	$value->outertext = '';
			    }
			    foreach ($html->find('.banner_468') as $value) {
			    	$value->outertext = '';
			    }
			    foreach ($html->find('#sp_lien_quan') as $value) {
			    	$value->outertext = '';
			    }
			    $html ->load($html ->save());
			    $tieude = $html->find('.title_news h1',0);
			    $img = $html->find('.fck_detail img',0)->src;
			    if($html->find('.fck_detail',0)!= null) {
			    	$noidung = $html->find('.fck_detail',0);
			    } else {
			    	$noidung = $html->find('#box_details_news .w670',0);
			    }
			    
			    $my_post = array(
			      'post_title'    => $tieude->plaintext,
			      'post_content'  => $noidung->innertext,
			      'post_status'   => $status,
			      'post_author'   => 1,
			      'post_category' => array($cat)
			    );
			    if(gnv_check_link($url)) { ?>
			    	<div class="clear"></div>
					<br>
					<div class="alert alert-danger">
					    <p><?php echo $url; ?>: Đã tồn tại!</p>
					</div>
			    <?php } else {
				    $id = wp_insert_post( $my_post );
				    Generate_Featured_Image($img,$id);
					update_post_meta($id, 'link_get_content', $url); ?>
					<div class="clear"></div>
					<br>
					<div class="alert alert-success">
					    <p>Post link '<?php echo $url; ?>' Thành công!</p>
					</div>
				<?php } ?>
			<?php } else { ?>
				<div class="clear"></div>
				<br>
				<div class="alert alert-danger">
				    <p><?php echo $url; ?> - Sai địa chỉ website</p>
				</div>
			<?php } ?>
	<?php }

	function Generate_Featured_Image($image_url, $post_id  ){
		if(empty($image_url)) return;
		$upload_dir = wp_upload_dir();
		$image_data = file_get_contents($image_url);
		$filename = basename($image_url);

		if(wp_mkdir_p($upload_dir['path'])){
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		file_put_contents($file, $image_data);

		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'guid'           => $upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name($filename),
			'post_content' => '',
			'post_status' => 'inherit'
			);
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		$res1= wp_update_attachment_metadata( $attach_id, $attach_data );
		$res2= set_post_thumbnail( $post_id, $attach_id );
	}

	function gnv_curPageURL() {
		$pageURL = 'http';
		if (!empty($_SERVER['HTTPS'])) {
		  if ($_SERVER['HTTPS'] == 'on') {
		    $pageURL .= "s";
		  }
		}
		$pageURL .= "://";
		if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
		} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

	function gnv_get_meta_values($key = '', $type = 'post') {
		global $wpdb;
		if(empty($key)) return;
		$r = $wpdb->get_col($wpdb->prepare("
			SELECT pm.meta_value FROM {$wpdb->postmeta} pm
			LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
			WHERE pm.meta_key = '%s' 
			AND p.post_type = '%s'
			", $key, $type));
		return $r;
	}
	
	function gnv_check_link($link) {
		$meta_links = gnv_get_meta_values("link_get_content");
		$i = 0;
		foreach ($meta_links as $value) {
			if($value == $link){
				$i++;	
			}
		}
		if($i==0) {
			return false;
		} else {
			return true;
		}
	}

	function gnv_check_link_die($url=NULL) 
	{ 
		if($url == NULL) return false; 
		$response = wp_remote_retrieve_response_code( wp_remote_get( $url, array( 'timeout' => 120, 'httpversion' => '1.1' )) );
		if($response!=200){ 
			return false; 
		} else { 
			return true; 
		} 
	}