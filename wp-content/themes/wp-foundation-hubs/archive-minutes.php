<?php get_header(); ?>
			
			<div id="content" class="clearfix">

				<?php get_sidebar('sidebarleft'); // sidebar left ?>
			
				<div id="main" class="ten columns clearfix" role="main">
				
					<h1 class="archive_title h2">
						Meeting Minutes
					</h1>

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article">
						
						<header>
							
							<h3 class="h2"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
							
							<p class="meta"><?php _e("Posted", "bonestheme"); ?> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> 
								<?php _e("by", "bonestheme"); ?> <?php the_author_posts_link(); ?> 
								<span class="amp">|</span>  <?php echo the_terms( $post->ID, 'minutes-category', '', ', ', ' ' ); ?></p>
								<?php the_tags('<p class="tags"><span class="tags-title">Tags:</span> ', ' ', '</p>'); ?>
						
						</header> <!-- end article header -->
					
						<section class="post_content">
						
							<?php the_post_thumbnail( 'bones-thumb-300' ); ?>
						
							<?php the_excerpt(); ?>
					
						</section> <!-- end article section -->
						
						<footer>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					<?php endwhile; ?>	
					
					<?php if (function_exists('page_navi')) { // if expirimental feature is active ?>
						
						<?php page_navi(); // use the page navi function ?>

					<?php } else { // if it is disabled, display regular wp prev & next links ?>
						<nav class="wp-prev-next">
							<ul class="clearfix">
								<li class="prev-link"><?php next_posts_link(_e('&laquo; Older Entries', "bonestheme")) ?></li>
								<li class="next-link"><?php previous_posts_link(_e('Newer Entries &raquo;', "bonestheme")) ?></li>
							</ul>
						</nav>
					<?php } ?>
								
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1><?php _e("No Posts Yet", "bonestheme"); ?></h1>
					    </header>
					    <section class="post_content">
					    	<p><?php _e("Sorry, What you were looking for is not here.", "bonestheme"); ?></p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
    
				<?php// get_sidebar('sidebarright'); // sidebar right ?>
    
			</div> <!-- end #content -->

<?php get_footer(); ?>