<div class="container">
  <div class="row ">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'stadium'));?>
    </div>
    <div class="col-md-10">

      <h4>Edit/Add Stadium</h4><hr/><br/>
      <p class="<?php echo ($result['status'])?'alert alert-danger':'';?>"><?php echo $result['message'];?></p>
      <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Title </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="title" value = "<?php echo $stadium['title'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Minimum Relics Count  </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="relics_count_min" value = "<?php echo $stadium['relics_count_min'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Maximum Relics Count  </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="relics_count_max" value = "<?php echo $stadium['relics_count_max'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="<?php echo getComponentUrl('stadium', 'listStadium');?>">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
