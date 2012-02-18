<?php
	$like = array('like' => '', 'unlike' => '');
	if($i_like == "1")
		$like = array('like' => ' ui_likes_sel', 'unlike' => '');
	elseif($i_like == "-1")
		$like = array('like' => '', 'unlike' => ' ui_likes_sel');
?>
<div class="ui_likes">
	<a href="javascript:void(0);" class="ui_like<?php echo $like['like']; ?> like"><?php echo lang('like'); ?></a>
	<a href="javascript:void(0);" class="ui_like<?php echo $like['unlike']; ?> unlike"><?php echo lang('unlike'); ?></a>
</div>