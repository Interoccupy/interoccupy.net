<?php
/*
Template Name: InterOcc Member Directory
*/
?>

<?php get_header(); ?>
      
      <div id="content">
      
        <div id="main" class="twelve columns" role="main">
          
          <article role="article">

            
            <header>
              
            </header>

            <section class="row post_content">

              <?php get_sidebar(); // sidebar 1 ?>

            
              <div class="nine columns">
            
                <?php the_content(); ?>

                <!-- start blog directory -->
                <?php
                // Get the authors from the database ordered randomly
                global $wpdb;
                $query = "SELECT ID, user_nicename from $wpdb->users WHERE ID != '1' ORDER BY RAND() LIMIT 50";
                $author_ids = $wpdb->get_results($query);
              
                // Loop through each author
                foreach($author_ids as $author) {
                  // Get user data
                  $curauth = get_userdata($author->ID);
                  
                  // Get link to author page
                  $user_link = get_author_posts_url($curauth->ID);
                  
                  // Get blog details for the authors primary blog ID
                  $blog_details = get_blog_details($curauth->primary_blog);
                  
                  if ($blog_details->post_count == "1") {
                    $postText = "post ";
                  }
                  else if ($blog_details->post_count >= "2") {
                    $postText = "posts";
                  }
                  else {
                    $postText = "posts";
                  }
                  $updatedOn = strftime("%m/%d/%Y at %l:%M %p",strtotime($blog_details->last_updated));
                  if ($blog_details->post_count == "") {
                    $blog_details->post_count = "0";
                  }
                  $posts = $wpdb->get_col( "SELECT ID FROM wp_".$curauth->primary_blog."_posts WHERE post_status='publish' AND post_type='post' AND post_author='$author->ID' ORDER BY ID DESC LIMIT 5");
                  $postHTML = "";
                  $i=0;
                  foreach($posts as $p) {
                    $postdetail=get_blog_post($curauth->primary_blog,$p);
                    if ($i==0) {
                      $updatedOn = strftime("%m/%d/%Y at %l:%M %p",strtotime($postdetail->post_date));
                    }
                    $postHTML .= "&#149; <a href=\"$postdetail->guid\">$postdetail->post_title</a><br />";
                    $i++;
                  }
                  ?>
                  <div class="author_bio">
                  <div class="row">
                  <div class="column grid_2">
                  <a href="<?php echo $blog_details->siteurl; ?>"><?php echo get_avatar($curauth->user_email, '96','http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536'); ?></a>
                  </div>
                  <div class="column grid_6">
                  <a href="<?php echo $blog_details->siteurl; ?>" title="<?php echo $curauth->display_name; ?> - <?=$blog_details->blogname?>"><?php //echo $curauth->display_name; ?> <?=$curauth->display_name;?></a><br />
                  <small><strong>Updated <?=$updatedOn?></strong></small><br />
                  <?php echo $curauth->description; ?>
                  </div>
                  <div class="column grid_3">
                  <h3>Recent Posts</h3>
                  <?=$postHTML?>
                  </div>
                  </div>
                  <span class="post_count"><a href="<?php echo $blog_details->siteurl; ?>" title="<?php echo $curauth->display_name; ?>"><?=$blog_details->post_count?><br /><?=$postText?></a></span>
                  </div>
                <?php } ?>
                <!-- end blog directory -->
                
              </div>
              
              <?php// get_sidebar('sidebar2'); // sidebar 2 ?>
                          
            </section> <!-- end article header -->
            
            
            <footer>
      
              <p class="clearfix"><?php the_tags('<span class="tags">Tags: ', ', ', '</span>'); ?></p>
              
            </footer> <!-- end article footer -->
          
          </article> <!-- end article -->
          
          <?php 
            // No comments on homepage
            //comments_template();
          ?>
      
        </div> <!-- end #main -->
        
      </div> <!-- end #content -->

<?php get_footer(); ?>