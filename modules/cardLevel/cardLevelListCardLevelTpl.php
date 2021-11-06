<script>
$(document).ready(function() {
    $('#card').DataTable();
} );
</script>

<div class="container">
  <div class="row">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'cardLevel'));?>
    </div>
    <div class="col-md-10">
      <div class="row">
        <div class="col-md-12">
        <h4>Card Level List</h4><hr/><br/>
          <div class="text-right"><a href="<?php echo getComponentUrl('cardLevel', 'add');?>">+Add New</a></div><br/>
          <table id="card" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
              <th>Master Card Level Up Id</th>
              <th>Card Level id</th>
              <th>Rarity Type</th>
              <th>Xp</th>
              <th>Gold</th>
              <th>Card Count</th>
              <th>Operation </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($cardLevelList as $cardLevel) {?>
            <tr>
              <td><?php echo $cardLevel['master_card_level_upgrade_id'];?></td>
              <td><?php echo $cardLevel['level_id'];?></td>
              <td><?php echo ($cardLevel['rarity_type'] == CARD_RARITY_COMMON)?'Common':(($cardLevel['rarity_type'] == CARD_RARITY_RARE)?'Rare':'Ultra Rare');?></td>
              <td><?php echo $cardLevel['xp'];?></td>
              <td><?php echo $cardLevel['gold'];?></td>
              <td><?php echo $cardLevel['card_count'];?></td>
                <td><?php if($cardLevel['status'] == CONTENT_ACTIVE)  { ?>
                <a href="<?php echo getComponentUrl('cardLevel', 'add', array('masterCardLevelUpgradeId'=>$cardLevel['master_card_level_upgrade_id']));?>" ?>Edit </a>|
                <a href="<?php echo getComponentUrl('cardLevel', 'delete', array('masterCardLevelUpgradeId'=>$cardLevel['master_card_level_upgrade_id']));?>" onclick="return confirm('Are you sure you want to delete this record?')"?>Delete</a>              <?php } ?>
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
