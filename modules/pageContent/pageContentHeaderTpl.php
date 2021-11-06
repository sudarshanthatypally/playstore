
<header>
  <div class="container">
    <div class="row header-block">
      <div class="col-lg-12">
        <h1><a href="/"><?php echo getConfig('project_title');?></a></h1>
        <?php if(isLoggedInUser()) { ?>
        <hr/>
        <div class="nav-block">
          <ul role="tablist" class="nav nav-pills nav-justified">
            <li class="<?php echo ($page=="home")?"active":"";?>"><a href="<?php echo getComponenturl('home', 'admin');?>">Home</a></li>
            <li class="<?php echo in_array($page, array("card","stadium","userLevel","cardLevel", "achievement","badge"))?"active":"";?>"><a href="<?php echo getComponenturl('card', 'listMasterCard');?>">Master</a></li>
            <li class="<?php echo ($page=="logout")?"active":"";?>"><a href="<?php echo getComponenturl('home', 'index');?>">Logout</a></li>
          </ul>
        </div>
        <?php } ?>
      </div>
    </div>
    <hr/>
  </div>
</header>
