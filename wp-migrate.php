<?php
require_once('wp-load.php');
$stylesheet_url = 'wp-content/themes/'.get_option('template').'/style.css';
?>
<html>
	<head>
		<title>Migrate WP Site</title>
		<?php
		if ( file_exists($stylesheet_url) ) {
			echo '<link rel="stylesheet" type="text/css" href="'.$stylesheet_url.'" />';
		}
		?>
	</head>
	
	<body>
	<h1>Migrate WP Site</h1>
	<p>This script will update all references in the DB to a new URL.</p>
	<ul>
		<li>The current WP URL is: <strong><?php echo get_bloginfo('url'); ?></strong></li>
		<li>Database: <?php echo DB_NAME; ?></li>
		<li>DB Host: <?php echo DB_HOST; ?></li>
	</ul>
	
	<form action="#" method="post">
		<?php
		global $wpdb;
		
		if ( !$_POST['submit_update_url'] ) { //FORM HAS NOT BEEN SUBMITTED
		?>
		<label for="old_url">Current URL</label>
		<input type="text" name="old_url" value="<?php echo get_bloginfo('url'); ?>" class="text" />
		
		<label for="new_url">New URL</label>
		<input type="text" name="new_url" class="text" />
		
		<input type="submit" name="submit_update_url" value="Submit" class="button " />
		<?php
		} else {
			extract($_POST);
			
			//CHECK FOR ERRORS
			if ( !$old_url || !$new_url ) {
				die('You must enter both URLs');
			}
			
			//SHOW SQL ERRORS
			$wpdb->show_errors();
			
			echo '<h2>Updating Database...</h2>';
			echo '<ul>';
			$error = false;
			
			$options_query = "UPDATE $wpdb->options SET option_value = replace(option_value, '$old_url', '$new_url') WHERE option_name = 'home' OR option_name = 'siteurl'";
			if ( $wpdb->query($options_query) ) {
				echo '<li>Options table updated successfully.</li>';
			} else {
				echo '<li>Problem updating options table.</li>';
				echo '<li>'.$wpdb->print_error().'</li>';
				$error = true;
			}
			
			$posts_query = "UPDATE $wpdb->posts SET guid = replace(guid, '$old_url','$new_url')";
			if ( $wpdb->query($posts_query) ) {
				echo '<li>Posts table updated successfully.</li>';
			} else {
				echo '<li>Problem updating posts table.</li>';
				echo '<li>'.$wpdb->print_error().'</li>';
				$error = true;
			}			
			
			$items_query = "UPDATE $wpdb->posts SET post_content = replace(post_content, '$old_url', '$new_url')";
			if ( $wpdb->query($items_query) ) {
				echo '<li>All posts/pages updated successfully.</li>';
			} else {
				echo '<li>Problem updating posts/pages.</li>';
				echo '<li>'.$wpdb->print_error().'</li>';
				$error = true;
			}
			
			if (!$error) {
				echo '<li>All done. <a href="'.$new_url.'">Go to home page</a> | <a href="'.$new_url.'/migrate.php">Another migration</a></li>';
			}
			
			
			echo '</ul>';
			
		}
		?>
	</form>
	</body>
</html>