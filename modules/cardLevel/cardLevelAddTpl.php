<div class="container">
  <div class="row ">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'cardLevel'));?>
    </div>
    <div class="col-md-10">

      <h4>Edit/Add Card Level</h4><hr/><br/>
      <p class="<?php echo ($result['status'])?'alert alert-danger':'';?>"><?php echo $result['message'];?></p>
      <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Card Level </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="level_id" value = "<?php echo $cardLevel['level_id'];?>" <?php if(!empty($cardLevel)) echo 'readonly'; ?>>
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Rarity Type  </label>
          <div class="col-sm-4">
            <select class="form-control" name="rarity_type" >
              <option value="1" <?php if($cardLevel['rarity_type']=="1") echo 'selected="selected"'; ?> >Common</option>
              <option value="2"  <?php if($cardLevel['rarity_type']=="2") echo 'selected="selected"'; ?>>Rare</option>
              <option value="3"  <?php if($cardLevel['rarity_type']=="3") echo 'selected="selected"'; ?>>Ultra Rare</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Xp  </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="xp" value = "<?php echo $cardLevel['xp'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Gold  </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="gold" value = "<?php echo $cardLevel['gold'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Card Count </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="card_count" value = "<?php echo $cardLevel['card_count'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="<?php echo getComponentUrl('cardLevel', 'listCardLevel');?>">Cancel</a>
          </div>
        </div>

    </form>
    </div>
  </div>
</div>
