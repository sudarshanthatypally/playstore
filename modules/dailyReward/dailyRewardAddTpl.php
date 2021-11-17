<div class="container">
  <div class="row ">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'dailyReward'));?>
    </div>
    <div class="col-md-10">

      <h4>Add/Edit Daily Reward</h4><hr/><br/>
      <p class="<?php echo ($result['status'])?'alert alert-danger':'';?>"><?php echo $result['message'];?></p>
      <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Title </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="title" value = "<?php echo $card['title'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Inventory </label>
          <div class="col-sm-4">
            <select class="form-control" name="master_inventory_id">
                <?php foreach ($inventoryList as $inventory) { ?>
                <option value="<?php echo $inventory['master_inventory_id'];?>"  <?php if($dailyRewardItemInventory['reward_item_id']==$inventory['master_inventory_id']) echo 'selected="selected"'; ?>><?php echo $inventory['name'];?></option>
                <?php } ?>
              </select>
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Cards </label>
          <div class="col-sm-4">
            <select class="form-control" name="master_card_id">
                <?php foreach ($masterCardList as $masterCard) { ?>
                <option value="<?php echo $masterCard['master_card_id'];?>"  <?php if($dailyRewardItemCard['reward_item_id']==$masterCard['master_card_id']) echo 'selected="selected"'; ?>><?php echo $masterCard['title'];?></option>
                <?php } ?>
              </select>
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Cube </label>
          <div class="col-sm-4">
            <select class="form-control" name="cube_id">
                <?php foreach ($cubeList as $cube => $value) { ?>
                <option value="<?php echo $value;?>"  <?php if($dailyRewardItemCube['reward_item_id']==$value) echo 'selected="selected"'; ?>><?php echo$cube;?></option>
                <?php } ?>
              </select>
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Stadium </label>
          <div class="col-sm-4">
            <select class="form-control" name="master_stadium_id">
                <?php foreach ($stadiumList as $stadium) { ?>
                <option value="<?php echo $stadium['master_stadium_id'];?>"  <?php if($card['master_stadium_id']==$stadium['master_stadium_id']) echo 'selected="selected"'; ?>><?php echo $stadium['title'];?></option>
                <?php } ?>
              </select>
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Crystal</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="crystal" value = "<?php echo $dailyReward['crystal'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="<?php echo getComponentUrl('dailyReward', 'list');?>">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
