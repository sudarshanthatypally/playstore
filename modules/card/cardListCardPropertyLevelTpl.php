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
        <h4>Card Property Level List</h4><hr/><br/>
        <div class="text-right"><a href="<?php echo getComponentUrl('card', 'addCardPropertyLevel', array('masterCardId' => $_GET['masterCardId'], 'cardPropertyId' => $_GET['cardPropertyId']));?>" ?>+Add New</a></div><br/>

          <table id="card" class="table table-striped table-bordered" cellspacing="0" width="100%" >
            <thead>
              <tr>
                <th>Card Property Level Upgrade Id</th>
                <th>Level Id</th>
                <th>Card Property Value</th>
                <th>Operation</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($cardPropertyLevelList as $propertyLevel) {?>
              <tr>
                <td><?php echo $propertyLevel['card_property_level_upgrade_id'];?></td>
                <td><?php echo $propertyLevel['level_id'];?></td>
                <td><?php echo $propertyLevel['card_property_value'];?></td>
                <td><?php if($propertyLevel['status'] == CONTENT_ACTIVE)  { ?>
                  <a href="<?php echo getComponentUrl('card', 'addCardPropertyLevel', array('cardPropertyLevelUpgradeId' => $propertyLevel['card_property_level_upgrade_id'], 'masterCardId' => $_GET['masterCardId'], 'cardPropertyId' => $_GET['cardPropertyId']));?>" ?>Edit | </a>
                  <a href="<?php echo getComponentUrl('card', 'deleteCardPropertyLevel', array('cardPropertyLevelUpgradeId' => $propertyLevel['card_property_level_upgrade_id'], 'masterCardId' => $_GET['masterCardId'], 'cardPropertyId' => $_GET['cardPropertyId']));?>" onclick="return confirm('Are you sure you want to delete this record?')"?>Delete</a>
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
