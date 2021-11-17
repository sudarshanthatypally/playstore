<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <meta name="viewport" content="width=device-width">
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <title>Reset Password</title>
    </head>
    <style>

    body{
      font-family: sans-serif;
    }

    .hr-style{
      margin-left:28px;
      margin-right:28px;
      color:orange;
      height: 2px;
      background-color: orange;
      border: none;
    }

    .form-control{
      display: inline-block;
      border: none;
      border-radius: 4px;
      box-sizing: border-box;
      width: 80%;
      padding: 12px 20px;
      margin: 8px -10px;
      background-color: #EBEEF2;
    }

    .button{
      text-decoration: none;
      font-size:20px;
      width:141px;
      color: #FFF;
      background-color: #F5A32B;
      font-family:GarField;
      border: 0;
      box-shadow:none;
      cursor: pointer;
      display: inline-block;
      padding:13px;
      border-radius: 6px;
    }

    .anchor-button{
      text-decoration: none;
      width:141px;
      color: #FFF;
      background-color: #F5A32B;
      font-size:20px;
      border:solid F4C851;
      border-width:5px 10px;
      text-align: center;
      padding:13px;
      cursor: pointer;
      line-height:23px;
      padding:12px;
      display: inline-block;
      border-radius: 6px;
    }

    h1{

       margin: -53px 0 5px !important;
       left:20px;
    }
      /* -------------------------------------
          RESPONSIVE AND MOBILE FRIENDLY STYLES
      ------------------------------------- */
      @media only screen and (max-width: 640px) {
          .container {
              width: 100% !important;
          }

          .anchor-button{
            width:118px;
            margin-top: 2px;
          }
      }

      @media
      only screen and (max-width: 760px)
      (min-device-width: 768px) and (max-device-width: 1024px)  {

      	/* Force table to not be like tables anymore */
      	table, thead, tbody, th, td, tr {
      		display: block;
      	}


      }
    </style>
    <script>
     function RestrictSpace(e) {
       var charCode = e.which || e.keyCode;
        if (charCode == 32) {
          return false;
        }
      }
    </script>
    <body style="margin: 0;padding: 0;box-sizing: border-box;font-size: 14px;-webkit-font-smoothing: antialiased;-webkit-text-size-adjust: none;height: 100%;line-height: 1.6;width: 100% !important;">

      <form action="" method="POST" class="form-horizontal">
        <table style="margin: 0;padding: 0;width: 100%;">
          <tr>
            <td></td>
            <td class="container" width="600" style="margin: 0 auto !important;padding: 0;vertical-align: top;display: block !important;max-width: 600px !important;clear: both !important;">
              <div style="margin: 0 auto;padding: 20px;max-width: 600px;display: block;overflow-x:auto;">
                <table width="100%" cellpadding="0" cellspacing="0" style="font-family:GarField;margin: 0;padding: 0;border: 1px solid  #D3D3D3;border-radius: 3px;">
                  <tr style="margin: 0;padding: 0;">
                    <td colspan="2">
            <!--          <img style="width:100%;" src="<?php echo getConfig('base_domain_path').getConfig('static_path_image')."/header.png"?>" /> -->
                    </td>
                  </tr>
                  <tr><td>&nbsp;&nbsp;</td></tr>
                  <tr><td>&nbsp;&nbsp;</td></tr>
                  <tr><td>&nbsp;&nbsp;</td></tr>
                  <tr><td>
                    &nbsp;&nbsp;</td></tr>
                  <tr>
                    <td colspan="2" style="text-align:center; font-size:25px; color:#7AB447;font-weight:bold; ">RESET PASSWORD</td>
                  </tr>
                  <tr>
                    <td colspan="2" style="text-align:center; font-size:25px; color:#7AB447;font-weight:bold; ">USER NAME: <?php echo $userName; ?></td>
                  </tr>
                  <tr><td  colspan="2"><hr class="hr-style" /></td></tr>
                    <?php if($result['status']) { ?>
                    <tr><td> <br /> <p class="<?php echo ($result['status']==1)?'bg-danger':'alert-success';?>"><?php echo $result['message'];?></p></td></tr>
                    <?php } else { ?>
                    <tr>
                      <td style="color:#7AB447;font-size:16px; font-weight:100px; padding-left:30px;" ><br />&nbsp;&nbsp;NEW PASSWORD</td>
                      <td><br /><input type="password" class="form-control" name="new_password" onkeypress="return RestrictSpace(event)" required></td><br />
                    </tr>
                    <tr>
                      <td style="color:#7AB447;font-size:16px; padding-left:30px;" ><br />&nbsp;&nbsp;CONFIRM PASSWORD</td>
                      <td><br /><input type="password" class="form-control" name="retype_new_password"  onkeypress="return RestrictSpace(event)" required></td>
                    </tr>
                    <tr>
                      <td colspan="2" style="padding-left:86px;"><br /><br />
                        <button type="submit" class="button" value="submit" name="submit">&nbsp;SAVE</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="<?php echo getComponentUrl('user', 'resetPassword');?>" class="anchor-button" >&nbsp;CANCEL</a>
                      </td>
                    </tr>
                    <?php } ?>

                </table>
              </div>
            </td>
          </tr>
        </table>
      </form>
    </body>
  </html>
