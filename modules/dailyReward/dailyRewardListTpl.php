<script>
$(document).ready(function() {
    $('#reward').DataTable();
} );

</script>

<div class="container">
  <div class="row">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'dailyReward'));?>
    </div>
    <div class="col-md-10">
      <div class="row">
        <div class="col-md-12">
        <h4>Reward List</h4><hr/><br/>
          <div class="text-right"><a href="<?php echo getComponentUrl('dailyReward', 'add');?>">+Add New</a></div><br/>
          <table id="reward" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
              <th>Daily Reward Id</th>
              <th>Title</th>
              <th>Crystal</th>
              <th>Operation</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($rewardList as $reward) {?>
            <tr>
              <td><?php echo $reward['master_daily_reward_id'];?></td>
              <td><?php echo $reward['title'];?></td>
              <td><?php echo $reward['crystal'];?></td>
              <td><?php if($reward['status'] == CONTENT_ACTIVE)  { ?>
                <a href="<?php echo getComponentUrl('card', 'addMasterCard', array('masterCardId'=>$card['master_card_id']));?>" ?>Edit |</a>
                <a href="<?php echo getComponentUrl('card', 'delete', array('masterCardId'=>$card['master_card_id']));?>" onclick="return confirm('Are you sure you want to delete this record?')"?>Delete | </a>
                <a href="<?php echo getComponentUrl('card', 'listCardProperty', array('masterCardId'=>$card['master_card_id']));?>" ?>Property</a>
              <?php } ?>
              </td>
            </tr>
            <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
