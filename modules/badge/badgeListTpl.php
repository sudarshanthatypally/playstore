<script>
$(document).ready(function() {
    $('#badge').DataTable();
} );
</script>

<div class="container">
  <div class="row">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'badge'));?>
    </div>
    <div class="col-md-10">
      <div class="row">
        <div class="col-md-12">
        <h4>badge List</h4><hr/><br/>
          <div class="text-right"><a href="<?php echo getComponentUrl('badge', 'add');?>">+Add New</a></div><br/>
          <table id="badge" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
              <th>Master badge Id</th>
              <th>Title</th>
              <th>Min Relic Count</th>
              <th>Max Relic Count</th>
              <th>Operation</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($badgeList as $badge) {?>
            <tr>
              <td><?php echo $badge['master_badge_id'];?></td>
              <td><?php echo $badge['title'];?></td>
              <td><?php echo $badge['min_relic_count'];?></td>
              <td><?php echo $badge['max_relic_count'];?></td>
              <td><?php if($badge['status'] == CONTENT_ACTIVE)  { ?>
                <a href="<?php echo getComponentUrl('badge', 'add', array('masterBadgeId'=>$badge['master_badge_id']));?>" ?>Edit | </a>
                <a href="<?php echo getComponentUrl('badge', 'delete', array('masterBadgeId'=>$badge['master_badge_id']));?>" onclick="return confirm('Are you sure you want to delete this record?')"?>Delete</a>
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
