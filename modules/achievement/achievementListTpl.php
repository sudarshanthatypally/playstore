<script>
$(document).ready(function() {
    $('#achievement').DataTable();
} );
</script>

<div class="container">
  <div class="row">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'achievement'));?>
    </div>
    <div class="col-md-10">
      <div class="row">
        <div class="col-md-12">
        <h4>Achievement List</h4><hr/><br/>
          <div class="text-right"><a href="<?php echo getComponentUrl('achievement', 'add');?>">+Add New</a></div><br/>
          <table id="achievement" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
              <th>  Master Achievement Id</th>
              <th>Title</th>
              <th>description</th>
              <th>Achievement Type</th>
              <th>xp</th>
              <th>Count</th>
              <th>Cube</th>
              <th>Operation</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($achievementList as $achievement) {?>
            <tr>
              <td><?php echo $achievement['master_achievement_id'];?></td>
              <td><?php echo $achievement['title'];?></td>
              <td><?php echo $achievement['description'];?></td>
              <td><?php echo $achievement['achievement_type'];?></td>
              <td><?php echo $achievement['xp'];?></td>
              <td><?php echo $achievement['count'];?></td>
              <td><?php echo $achievement['cube_id'];?></td>
              <td><?php if($achievement['status'] == CONTENT_ACTIVE)  { ?>
                <a href="<?php echo getComponentUrl('achievement', 'add', array('masterAchievementId'=>$achievement['master_achievement_id']));?>" ?>Edit | </a>
                <a href="<?php echo getComponentUrl('achievement', 'delete', array('masterAchievementId'=>$achievement['master_achievement_id']));?>" onclick="return confirm('Are you sure you want to delete this record?')"?>Delete</a>
              <?php } ?>
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
