<script>
$(document).ready(function() {
    $('#card').DataTable();
} );
</script>

<div class="container">
  <div class="row">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'userLevel'));?>
    </div>
    <div class="col-md-10">
      <div class="row">
        <div class="col-md-12">
          <h4>User Level List</h4><hr/><br/>
          <div class="text-right"><a href="<?php echo getComponentUrl('userLevel', 'add');?>">+Add New</a></div><br/>
          <table id="card" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>Master Level Up Id</th>
                <th>Level id</th>
                <th>Xp To Next Level</th>
                <th>God Tower Damage</th>
                <th>Stadium Tower Damage</th>
                <th>God Tower Health</th>
                <th>Stadium Tower Health</th>
                <th>Operation </th>
              </tr>
            </thead>
            <tbody>
            <?php foreach($userLevelList as $userLevel) {?>
            <tr>
              <td><?php echo $userLevel['master_level_up_id'];?></td>
              <td><?php echo $userLevel['level_id'];?></td>
              <td><?php echo $userLevel['xp_to_next_level'];?></td>
              <td><?php echo $userLevel['god_tower_damage'];?></td>
              <td><?php echo $userLevel['stadium_tower_damage'];?></td>
              <td><?php echo $userLevel['god_tower_health'];?></td>
              <td><?php echo $userLevel['stadium_tower_health'];?></td>

              <td><?php if($userLevel['status'] == CONTENT_ACTIVE)  { ?>
                <a href="<?php echo getComponentUrl('userLevel', 'add', array('masterLevelUpId'=>$userLevel['master_level_up_id']));?>" ?>Edit </a>|
                <a href="<?php echo getComponentUrl('userLevel', 'delete', array('masterLevelUpId'=>$userLevel['master_level_up_id']));?>" onclick="return confirm('Are you sure you want to delete this record?')">Delete</a>
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
