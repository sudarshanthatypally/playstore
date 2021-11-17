<div class="container">
  <div class="row ">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'achievement'));?>
    </div>
    <div class="col-md-10">

      <h4>Add/Edit Achievement</h4><hr/><br/>
      <p class="<?php echo ($result['status'])?'alert alert-danger':'';?>"><?php echo $result['message'];?></p>
      <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Title  </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="title"  value = "<?php echo $achievement['title'];?>" >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label"> Description </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="description"  value = "<?php echo $achievement['description'];?>" >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Achievement Type </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="achievement_type"  value = "<?php echo $achievement['achievement_type'];?>" >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Count </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="count"  value = "<?php echo $achievement['count'];?>" >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Cube Id </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="cube_id"  value = "<?php echo $achievement['cube_id'];?>" >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">xp </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="xp"  value = "<?php echo $achievement['xp'];?>" >
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="<?php echo getComponentUrl('achievement', 'list', array('masterAchievementId'=>$achievement['master_achievement_id']));?>">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
