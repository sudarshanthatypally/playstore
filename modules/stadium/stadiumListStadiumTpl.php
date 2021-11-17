<script>
$(document).ready(function() {
    $('#card').DataTable();
} );
</script>

<div class="container">
  <div class="row">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'stadium'));?>
    </div>
    <div class="col-md-10">
      <div class="row">
        <div class="col-md-12">
          <h4>Stadium List</h4><hr/><br/>
          <div class="text-right"><a href="<?php echo getComponentUrl('stadium', 'add');?>">+Add New</a></div><br/>
          <table id="card" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
              <tr>
                <th>Stadiumd Id</th>
                <th>Title</th>
                <th>Minimum Relics Count</th>
                <th>Maximum Relics Count</th>
                <th>Operation </th>
              </tr>
            </thead>
            <tbody>
            <?php foreach($stadiumList as $stadium) {?>
            <tr>
              <td><?php echo $stadium['master_stadium_id'];?></td>
              <td><?php echo $stadium['title'];?></td>
              <td><?php echo $stadium['relics_count_min'];?></td>
              <td><?php echo $stadium['relics_count_max'];?></td>

              <td><?php if($stadium['status'] == CONTENT_ACTIVE)  { ?>
                <a href="<?php echo getComponentUrl('stadium', 'add', array('stadiumId'=>$stadium['master_stadium_id']));?>" ?>Edit | </a>
                <a href="<?php echo getComponentUrl('stadium', 'delete', array('stadiumId'=>$stadium['master_stadium_id']));?>" onclick="return confirm('Are you sure you want to delete this record?')"?>Delete</a>
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
