<div class="container">
  <div class="row ">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'userLevel'));?>
    </div>
    <div class="col-md-10">

      <h4>Edit/Add User Level</h4><hr/><br/>
      <p class="<?php echo ($result['status'])?'alert alert-danger':'';?>"><?php echo $result['message'];?></p>
      <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Level </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="level_id" value = "<?php echo $userLevel['level_id'];?>" <?php if(!empty($userLevel)) echo 'readonly'; ?>>
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Xp to Next Level  </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="xp_to_next_level" value = "<?php echo $userLevel['xp_to_next_level'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">God Tower Health </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="god_tower_health" value = "<?php echo $userLevel['god_tower_health'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Stadium Tower Health </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="stadium_tower_health" value = "<?php echo $userLevel['stadium_tower_health'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">God Tower Damage </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="god_tower_damage" value = "<?php echo $userLevel['god_tower_damage'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Stadium Tower Damage </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="stadium_tower_damage" value = "<?php echo $userLevel['stadium_tower_damage'];?>" required >
          </div>
        </div>

        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="<?php echo getComponentUrl('userLevel', 'listUserLevel');?>">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
