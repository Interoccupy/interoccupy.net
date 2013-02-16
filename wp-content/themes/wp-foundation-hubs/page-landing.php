<?php
/*
Template Name: Section Landing Page
*/
?>
<?php get_header(); ?>
			
			<div id="content">

				<?php get_sidebar('sidebarleft'); // sidebar left ?>
			
				<div id="main" class="six columns clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
							
							<h1 class="page-title" itemprop="headline"><?php the_title(); ?></h1>
						
						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">
							<?php the_content(); ?>
					
						</section> <!-- end article section -->
						
						<footer>
			
							<?php the_tags('<p class="tags"><span class="tags-title">Tags:</span> ', ', ', '</p>'); ?>
							
						</footer> <!-- end article footer -->
					
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
    
				<div id="sidebarsubnav" class="sidebar three columns" role="complementary">

					<div class="panel">
				
					<?php
					$children = wp_list_pages('title_li=&child_of='.$post->ID.'&echo=0');
					if ($children) { ?>
						<ul>
						<?php echo $children; ?>
						</ul>
					<?php } ?>

					</div>

				</div>
    
			</div> <!-- end #content -->

<?php get_footer(); ?>