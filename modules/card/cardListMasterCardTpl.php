<script>
$(document).ready(function() {
    $('#card').DataTable();
} );

</script>

<div class="container">
  <div class="row">
    <div class="col-md-2 block">
      <?php echo page::renderComponent('pageContent','pageContentSidebar', array('page'=>'card'));?>
    </div>
    <div class="col-md-10">
      <div class="row">
        <div class="col-md-12">
        <h4>Card List</h4><hr/><br/>
          <div class="text-right"><a href="<?php echo getComponentUrl('card', 'addMasterCard');?>">+Add New</a></div><br/>
          <table id="card" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
              <th>Card Id</th>
              <th>Title</th>
              <th>Stadium Id</th>
              <th>Card Type</th>
              <th>Rarity Type</th>
              <th>Is card Default</th>
              <th>Card Max Level</th>
              <th>Card Description</th>
              <th>Operation</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($cardList as $card) {?>
            <tr>
              <td><?php echo $card['master_card_id'];?></td>
              <td><?php echo $card['title'];?></td>
              <td><?php echo $card['master_stadium_id'];?></td>
              <td><?php echo $card['card_type'];?></td>
              <td><?php echo $card['rarity_type'];?></td>
              <td><?php echo $card['is_card_default'];?></td>
              <td><?php echo $card['card_max_level'];?></td>
              <td><?php echo $card['card_description'];?></td>
              <td><?php if($card['status'] == CONTENT_ACTIVE)  { ?>
                <a href="<?php echo getComponentUrl('card', 'addMasterCard', array('masterCardId'=>$card['master_card_id']));?>" ?>Edit |</a>
                <a href="<?php echo getComponentUrl('card', 'delete', array('masterCardId'=>$card['master_card_id']));?>" onclick="return confirm('Are you sure you want to delete this record?')"?>Delete | </a>
                <a href="<?php echo getComponentUrl('card', 'listCardProperty', array('masterCardId'=>$card['master_card_id']));?>" ?>Property</a>
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
