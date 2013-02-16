<?php get_header(); ?>
			
			<div id="content" class="clearfix">
			
				<div id="main" class="twelve columns clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
													
							<h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1>
							
							<p class="meta"><?php _e("Posted", "bonestheme"); ?> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> 
								<?php _e("by", "bonestheme"); ?> <?php the_author_posts_link(); ?>
						
						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">

							<?php the_content(); ?>

							<p class="tags"><?php the_category(', '); ?></p>

							<?php
								$terms = get_the_terms( $post->ID , 'event-tags' );
								foreach ( $terms as $term ) {
								echo '<a href="' . get_term_link($term->slug, 'event-tags') . '" class="label success radius">' . $term->name . '</a>';
								}
							?>
							
						</section> <!-- end article section -->
						
					
					</article> <!-- end article -->
					
					<?php comments_template(); ?>
					
					<?php endwhile; ?>			
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1>Not Found</h1>
					    </header>
					    <section class="post_content">
					    	<p>Sorry, but the requested resource was not found on this site.</p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
    
				<?php// get_sidebar('sidebarright'); // sidebar right ?>
    
			</div> <!-- end #content -->

<?php get_footer(); ?>