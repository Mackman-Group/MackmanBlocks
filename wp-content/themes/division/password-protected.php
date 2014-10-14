<div class="bk-protected-form-fixed-wrap">
	<div class="bk-protected-form-outer-wrap">
	  <div class="bk-protected-form-inner-wrap">
	     <div class="bk-protected-form-content-wrap">
			<?php 
				echo  '<h1 class="page-entry-title">';
				echo  get_the_title(get_the_ID());
				echo  '</h1>';
				echo get_the_password_form(); 
			?>
		  </div>
	  </div>	
	</div>
</div>	