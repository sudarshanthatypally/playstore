<div class="container">
  <div class="row ">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'badge'));?>
    </div>
    <div class="col-md-10">

      <h4>Add/Edit badge</h4><hr/><br/>
      <p class="<?php echo ($result['status'])?'alert alert-danger':'';?>"><?php echo $result['message'];?></p>
      <form action="" method="POST" class="form-horizontal" enctype="multipart/form-data">
        <div class="form-group">
          <label for="title" class="col-sm-2 control-label">Title  </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="title"  value = "<?php echo $badge['title'];?>" >
          </div>
        </div>

        <div class="form-group">
          <label for="min_relic_count" class="col-sm-2 control-label"> Min Relics Count </label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="min_relic_count"  value = "<?php echo $badge['min_relic_count'];?>" >
          </div>
        </div>

        <div class="form-group">
          <label for="max_relic_count" class="col-sm-2 control-label">Max Relics Count</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="max_relic_count"  value = "<?php echo $badge['max_relic_count'];?>" >
          </div>
        </div>


        <div class="form-group">
          <div class="col-sm-offset-3 col-sm-9">
            <button type="submit" class="btn btn-primary">Submit</button>
            <a href="<?php echo getComponentUrl('badge', 'list', array('masterBadgeId'=>$badge['master_badge_id']));?>">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
