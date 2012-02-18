		<?php
			//Control Ads Control 
			$ads_control = new AdsControl();
			$ads_control->ini(array(
				"title"		=> lang("recommendations"),
				"url_img"	=> "application/images/chrome_recomendacion.png",
				"text"		=> lang('msg_ads'),
				"url_link"	=> "https://chrome.google.com/webstore/detail/njhbaeddcfakoochemlgbfjbhibncdca?hl=en-US"
			));
			$ads_control->printHtml(true);
		?>
		
		<div class="ads_control margin_top">
			<script type="text/javascript"><!--
			google_ad_client = "ca-pub-1623393240431425";
			/* YouHaveItOn_RightBlock */
			google_ad_slot = "4917292006";
			google_ad_width = 160;
			google_ad_height = 600;
			//-->
			</script>
			<script type="text/javascript"
			src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
			</script>
		</div>
		
		<a href="mailto:contact@youhaveiton.com" class="feedback">feedback</a>