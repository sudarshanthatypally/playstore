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
        <h4>Card Property List</h4><hr/><br/>
        <div class="text-right"><a href="<?php echo getComponentUrl('card', 'addCardProperty', array('masterCardId' => $_GET['masterCardId']));?>" ?>+Add New</a></div><br/>

          <table id="card" class="table table-striped table-bordered" cellspacing="0" width="100%" >
            <thead>
            <tr>
              <th>Property Id</th>
              <th>Card Property Id</th>
              <th>Property Name</th>
              <!-- <th>Property Value</th> -->
              <th>Is Default</th>
              <th>Operation</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($cardPropertyList as $cardProperty) {?>
            <tr>
              <td><?php echo $cardProperty['card_property_id'];?></td>
              <td><?php echo $cardProperty['property_id'];?></td>
              <td><?php echo $cardProperty['property_name'];?></td>
              <!-- <td><?php echo $cardProperty['card_property_value'];?></td> -->
              <td><?php echo $cardProperty['is_default'];?></td>
              <td><?php if($cardProperty['status'] == CONTENT_ACTIVE)  { ?>
                <a href="<?php echo getComponentUrl('card', 'addCardProperty', array('cardPropertyId'=>$cardProperty['card_property_id'], 'masterCardId'=>$_GET['masterCardId']));?>" ?>Edit | </a>
                <a href="<?php echo getComponentUrl('card', 'deleteCardProperty', array('cardPropertyId'=>$cardProperty['card_property_id'], 'masterCardId'=>$_GET['masterCardId']));?>" onclick="return confirm('Are you sure you want to delete this record?')"?>Delete | </a>
                <a href="<?php echo getComponentUrl('card', 'listCardPropertyLevel', array('cardPropertyId'=>$cardProperty['card_property_id'], 'masterCardId'=>$_GET['masterCardId']));?>" ?>PropertyLevel</a>
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
