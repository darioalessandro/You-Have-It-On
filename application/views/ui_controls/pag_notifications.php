<div id="home" class="margin_top">
	<div class="col_content dtlpostyle margin_right"> 
		<div id="notifications_detail" style="overflow: hidden; position: relative;">
			<div id="notify_conte" style="position: relative;width:2100px; left: -680px;">
				<div class="ponaqui" style="float:left;position: relative;width:680px; left: 680px;">
			<?php 
				if(is_array($data)){
					foreach($data['notifications'] as $key => $item){
						echo '<div class="notify_item">'.$item.'<div class="clear"></div></div>';
					}
				}
			?>
				</div>
			</div>
		</div>
		<div class="clear"></div>
		<div id="ui_pagination" class="ui_pagination">
		<?php 
			$ci =& get_instance();
			$ci->load->library('pagination');
		
			$paginar['base_url'] 		= '';
			$paginar['anchor_class']	= 'pag_notify';
			$paginar['total_rows'] 		= $data['num_notification'];
			$paginar['per_page'] 		= $result_items_per_page;
			$paginar['cur_page']		= $result_page;
			$paginar['javascript']		= 'javascript:void(0);';
			$paginar['first_link']		= false;
			$paginar['last_link']		= false;
			$paginar['display_pages']	= false;
			
			$ci->pagination->initialize($paginar);
			echo $ci->pagination->create_links();
		?>
		</div>
	</div>
	
	<div class="col_right">
		<?php
			//Control Ads Control 
			$this->load->view("ui_controls/pag_ads");
		?>
	</div>
	
	<div class="clear"></div>
</div>
