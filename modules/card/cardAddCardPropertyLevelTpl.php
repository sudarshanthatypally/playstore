<div class="container">
  <div class="row ">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'card'));?>
    </div>
    <div class="col-md-10">

      <h4>Add/Edit Card Property Level </h4><hr/><br/>
      <p class="<?php echo ($result['status'])?'alert alert-danger':'';?>"><?php echo $result['message'];?></p>
      <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Level Id </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="level_id"  value = "<?php echo $propertyLevelUp['level_id'];?>" required>
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Card Property Value</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="card_property_value" value = "<?php echo $propertyLevelUp['card_property_value'];?>">
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="<?php echo getComponentUrl('card', 'listCardPropertyLevel', array('cardPropertyId' => $_GET['cardPropertyId'], 'masterCardId' => $_GET['masterCardId']));?>">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
