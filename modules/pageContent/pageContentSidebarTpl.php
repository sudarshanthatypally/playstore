<table class="table">
  <tr><td class="<?php echo ($page=="card")?"active":"";?>"><a href="<?php echo getComponentUrl('card', 'listMasterCard')?>">Card </a></td></tr>
  <tr><td class="<?php echo ($page=="stadium")?"active":"";?>"><a href="<?php echo getComponentUrl('stadium', 'listStadium')?>">Stadium </a></td></tr>
  <tr><td class="<?php echo ($page=="userLevel")?"active":"";?>"><a href="<?php echo getComponentUrl('userLevel', 'listUserLevel')?>">User Level </a></td></tr>
  <tr><td class="<?php echo ($page=="cardLevel")?"active":"";?>"><a href="<?php echo getComponentUrl('cardLevel', 'listCardLevel')?>">Card Level </a></td></tr>
  <tr><td class="<?php echo ($page=="achievement")?"active":"";?>"><a href="<?php echo getComponentUrl('achievement', 'list')?>">Achievement </a></td></tr>
  <tr><td class="<?php echo ($page=="badge")?"active":"";?>"><a href="<?php echo getComponentUrl('badge', 'list')?>">Badge </a></td></tr>
  <tr><td class="<?php echo ($page=="dailyReward")?"active":"";?>"><a href="<?php echo getComponentUrl('dailyReward', 'list')?>">Daily Reward </a></td></tr>
</table>
