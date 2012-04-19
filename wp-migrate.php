<?php
require_once('wp-load.php');
?>
<html>
	<head>
		<title>Migrate WP Site</title>
		<style type="text/css">
		body {
			font-family:"Lucida Sans","Lucida Grande","Lucida Unicode","Lucida",sans-serif;
			background-color: #666;
		}
		
		h1 {
			font-size:24px;
			margin:0;
			padding:5px;
			color:#fff;
			text-shadow: 1px 1px rgba(0,0,0,0.2);
			background-color: rgba(0,0,0,0.6);
			-moz-border-radius:9px;
			-webkit-border-radius:9px;
			-khtml-border-radius:9px;
			border-radius:9px;
		}
		h2 {
			font-size: 18px;
		}
		#container {
			width:600px;
			margin:20px auto;
			border:1px solid #333;
			background-color: rgba(255,255,255,0.8);
			-moz-border-radius:10px;
			-webkit-border-radius:10px;
			-khtml-border-radius:10px;
			border-radius:10px;
		}
		#content {
			padding:20px;
		}
		p {
			font-size:14px;
			line-height: 18px;
			margin-bottom: 18px;
		}
			p:last-child {
				margin-bottom: 0;
			}
		a, a:visited {
			color:#20668a;
		}
		ul {
			margin:10px;
			padding: 0;
			list-style: square;
		}
		form {}
		label {
			display: block;
			font-weight:bold;
		}
		input[type=text] {
			display: block;
			-moz-border-radius:5px;
			-webkit-border-radius:5px;
			-khtml-border-radius:5px;
			border-radius:5px;
			width: 100%;
			margin-bottom: 10px;
			padding: 5px;	
		}
		input[type=submit] {
			color:#fff;
			background: #20668a;
			-moz-border-radius:5px;;
			-webkit-border-radius:5px;
			-khtml-border-radius:5px;
			border-radius:5px;
			text-shadow: 1px 1px #0b4462;
			box-shadow: 1px 1px 3px rgba(0,0,0,0.5);
			font-size:14px;
			font-weight:bold;
			position: relative;
			cursor: pointer;
			float:right;
			margin:10px 0;
			border:none;
		}
			input[type=submit]:active {
				left:1px;
				top:1px;
			}
		.message {
			padding:5px;
			margin:10px 0;
			border-width: 2px;
			border-style: solid;
			list-style: none;
			-moz-border-radius:5px;
			-webkit-border-radius:5px;
			-khtml-border-radius:5px;
			border-radius:5px;
		}
		.message.success {
			color:#30ac12;
			border-color: #30ac12;
			background-color: #afd9a5;
		}
		.message.warning {
			border-color: #efce15;
			color: #957236;
			background-color: #f2e8af;
		}
		.message.error {
			border-color: #c51414;
			color: #c51414;
			background-color: #f5a2a2;
		}
		</style>
	</head>
	
	<body>
		<div id="container">
			<h1>Migrate WordPress Site</h1>
			<div id="content">
				<?php
				global $wpdb;
				
				if ( !$_POST['submit_update_url'] ) { //FORM HAS NOT BEEN SUBMITTED
				?>
				<p class="message warning">This script will update all references in the DB to a new URL. Before proceeding, you should <a href="http://codex.wordpress.org/Backing_Up_Your_Database" target="_blank">make a complete backup of your database</a>.</p>
				<ul>
					<li>The current WP URL is: <strong><?php echo get_bloginfo('url'); ?></strong></li>
					<li>Database: <?php echo DB_NAME; ?></li>
					<li>DB Host: <?php echo DB_HOST; ?></li>
				</ul>
				
				<form action="#" method="post">
					<label for="old_url">Current URL</label>
					<input type="text" name="old_url" value="<?php echo get_bloginfo('url'); ?>" class="text" />
					
					<label for="new_url">New URL</label>
					<input type="text" name="new_url" class="text" />
					
					<input type="submit" name="submit_update_url" value="Update Database" class="button " />
					<p style="display:block;">&nbsp;</p>
				</form>
					<?php
					} else {
						extract($_POST);
						
						//CHECK FOR ERRORS
						if ( !$old_url || !$new_url ) {
							echo '<p class="message error">You must enter both the old and new URLs.</p>';
							die();
						}
						
						//SHOW SQL ERRORS
						$wpdb->show_errors();
						
						echo '<h2>Updating Database...</h2>';
						echo '<ul>';
						$error = false;
						
							$options_query = "UPDATE $wpdb->options SET option_value = replace(option_value, '$old_url', '$new_url') WHERE option_name = 'home' OR option_name = 'siteurl'";
							if ( $wpdb->query($options_query) ) {
								echo '<li class="message success">Options table updated successfully.</li>';
							} else {
								echo '<li class="message error">Problem updating options table.';
								echo $wpdb->print_error().'</li>';
								$error = true;
							}
							
							$posts_query = "UPDATE $wpdb->posts SET guid = replace(guid, '$old_url','$new_url')";
							if ( $wpdb->query($posts_query) ) {
								echo '<li class="message success">Posts table updated successfully.</li>';
							} else {
								echo '<li class="message error">Problem updating posts table. ';
								echo $wpdb->print_error().'</li>';
								$error = true;
							}			
							
							$items_query = "UPDATE $wpdb->posts SET post_content = replace(post_content, '$old_url', '$new_url')";
							if ( $wpdb->query($items_query) ) {
								echo '<li class="message success">All posts/pages updated successfully.</li>';
							} else {
								echo '<li class="message error">Problem updating posts/pages. ';
								echo $wpdb->print_error().'</li>';
								$error = true;
							}
							
							$meta_query = "UPDATE $wpdb->postmeta SET meta_value = replace(meta_value, '$old_url', '$new_url')";
							$meta_query = $wpdb->query($meta_query);
							if (  $meta_query !== false ) {
								echo '<li class="message success">All custom fields updated successfully. '.$meta_query.' fields were updated.</li>';
							} else {
								echo '<li class="message error">Problem updating custom fields. ';
								echo $wpdb->print_error().'</li>';
								$error = true;
							}
							
							if (!$error) {
								echo '<p class="message success">All done. <a href="'.$new_url.'">Go to home page</a> | <a href="'.$new_url.'/wp-migrate.php">Do another migration</a></p>';
								echo '<p class="message warning">You should delete the \'wp-migrate.php\' file from your root directory. Failing to do so is a major security risk and could cause your site to be hacked.</p>';
							}
						
						echo '</ul>';
						
					}
					?>
			</div><!--#content -->
		</div><!-- #container -->
	</body>
</html>