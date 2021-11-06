<div class="container">
  <div class="row ">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'card'));?>
    </div>
    <div class="col-md-10">

      <h4>Add/Edit Card Property</h4><hr/><br/>
      <p class="<?php echo ($result['status'])?'alert alert-danger':'';?>"><?php echo $result['message'];?></p>
      <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Card Property Id </label>
          <div class="col-sm-4">
            <select class="form-control" id="property_id" name="property_id" >
              <?php foreach ($cardMasterPropertyList as $cardMasterProperty) { ?>
                <option value="<?php echo $cardMasterProperty['master_property_id'];?>"  <?php if($cardMasterProperty['master_property_id']== $cardProperty['property_id']) echo 'selected="selected"'; ?>><?php echo $cardMasterProperty['master_property_id'];?></option>
              <?php } ?>
            </select>
          </div>
        </div>

        <!-- <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Card Property Value </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="card_property_value"  value = "<?php echo $cardProperty['card_property_value'];?>" required>
          </div>
        </div> -->

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Is Default </label>
          <div class="col-sm-4">
            <input type="radio" name="is_default" value="1" <?php if($cardProperty['is_default'] == 1) echo 'checked="checked"'; ?>> Default
            <input type="radio" name="is_default" value="0" <?php if($cardProperty['is_default'] == 0) echo 'checked="checked"'; ?>> Not Default
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="<?php echo getComponentUrl('card', 'listCardProperty', array('masterCardId'=>$_GET['masterCardId']));?>">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
