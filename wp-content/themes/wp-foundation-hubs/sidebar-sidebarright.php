				<div id="sidebarright" class="sidebar four columns" role="complementary">

					<div class="panel">
				
						<?php if ( is_active_sidebar( 'sidebarright' ) ) : ?>

							<?php dynamic_sidebar( 'sidebarright' ); ?>

						<?php else : ?>

							<!-- This content shows up if there are no widgets defined in the backend. -->
							
							<div class="alert-box">Please activate some Widgets.</div>

						<?php endif; ?>

					</div>

				</div>