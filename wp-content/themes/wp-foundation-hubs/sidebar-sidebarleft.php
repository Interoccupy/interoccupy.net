				<div id="sidebarleft" class="sidebar two columns" role="complementary">

					<div class="panel">
				
						<?php if ( is_active_sidebar( 'sidebarleft' ) ) : ?>

							<?php dynamic_sidebar( 'sidebarleft' ); ?>

						<?php else : ?>

							<!-- This content shows up if there are no widgets defined in the backend. -->
							
							<div class="alert-box">Please activate some Widgets.</div>

						<?php endif; ?>

					</div>

				</div>