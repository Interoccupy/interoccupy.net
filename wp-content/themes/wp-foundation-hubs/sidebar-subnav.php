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