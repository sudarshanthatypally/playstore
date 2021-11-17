<div class="container">
  <div class="row ">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'card'));?>
    </div>
    <div class="col-md-10">

      <h4>Add/Edit Master Card</h4><hr/><br/>
      <p class="<?php echo ($result['status'])?'alert alert-danger':'';?>"><?php echo $result['message'];?></p>
      <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Title </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="title" value = "<?php echo $card['title'];?>" required >
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
          <label for="title" class="col-sm-2 control-label">Card Type </label>
          <div class="col-sm-4">
            <select class="form-control" name="card_type">
              <option value="1"  <?php if($card['card_type']=="1") echo 'selected="selected"'; ?>>Character</option>
              <option value="2"  <?php if($card['card_type']=="2") echo 'selected="selected"'; ?>>Power</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Rarity Type </label>
          <div class="col-sm-4">
            <select class="form-control" name="rarity_type">
              <option value="1" <?php if($card['card_rarity_type']=="1") echo 'selected="selected"'; ?> >Common</option>
              <option value="2"  <?php if($card['card_rarity_type']=="2") echo 'selected="selected"'; ?>>Rare</option>
              <option value="3"  <?php if($card['card_rarity_type']=="3") echo 'selected="selected"'; ?>>Ultra Rare</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Is Card Default </label>
          <div class="col-sm-4">
            <input type="radio" name="is_card_default" value="1" <?php if($card['is_card_default']== 1) echo 'checked="checked"'; ?>/> Default card
            <input type="radio" name="is_card_default" value="0" <?php if($card['is_card_default']== 0) echo 'checked="checked"'; ?>/> Not Default Card
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Card Max Level </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="card_max_level" value = "<?php echo $card['card_max_level'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Card Description </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="card_description" value = "<?php echo $card['card_description'];?>" required >
          </div>
        </div>


        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="<?php echo getComponentUrl('card', 'listMasterCard');?>">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
