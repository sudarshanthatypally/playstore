<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
  <html xmlns="http://www.w3.org/1999/xhtml" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;">
    <head style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;">
      <meta name="viewport" content="width=device-width" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;">
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" style="margin: 0;padding: 0;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;box-sizing: border-box;font-size: 14px;">
      <title>Fun Run</title>
    </head>
    <style style="margin: 0;padding: 0;">
      td{
          font-family:gabriola-regular;
          font-style:italic;
        }

      h1{
         font-weight: 600 !important;
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
          h1, h2, h3, h4 {
             font-weight: 600 !important;
             margin: -35px 0 5px !important;
             left:20px;
         }
      }
    </style>
    <body style="margin: 0;padding: 0;box-sizing: border-box;font-size: 14px;-webkit-font-smoothing: antialiased;-webkit-text-size-adjust: none;height: 100%;line-height: 1.6;background-color: #f6f6f6;width: 100% !important;">
      <?php
        $userLib = autoload::loadLibrary('queryLib', 'user');
        $linkDetail = explode('&', $link);
        $length = sizeof($linkDetail);
        $userParam = $linkDetail[$length -1];
        $userId = explode('=', $userParam);
        $userDetail = $userLib->getUserDetail($userId[1]);
        $userName = $userDetail['user_name'];

      ?>
      <table style="margin: 0;padding: 0;background-color: #f6f6f6;width: 100%;">
        <tr style="margin: 0;padding: 0;">
          <td style="margin: 0;padding: 0;"></td>
          <td class="container" width="600" style="margin: 0 auto !important;padding: 0;vertical-align: top;display: block !important;max-width: 600px !important;clear: both !important;">
            <div style="margin: 0 auto;padding: 20px;max-width: 600px;display: block;">
              <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0;padding: 0;border: 1px solid #e9e9e9;border-radius: 3px;">
                <tr style="margin: 0;padding: 0;">
                  <td style="margin: 0;text-align: center;color: #F4C851;">
                    <img style="width:100%;" src="<?php echo getConfig('base_domain_path').getConfig('static_path_image')."/TitleBar1.png"?>" />
                  </td>
                </tr>
                <tr style="margin: 0;padding: 0;">
                  <td style="margin: 0;padding: 20px;">
                    <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0;padding: 0;box-sizing: border-box;">
                      <tr style="margin: 0;padding: 0;box-sizing: border-box;font-size: 14px;">
                        <td style="margin: 0;padding: 0 0 20px;">
                          <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0;padding: 0;box-sizing: border-box;font-size: 14px;">
                            <tr>
                              <td style="color: #000000;">Reset Password<br/><br/></td>
                            </tr>
                            <tr>
                              <td style="margin: 0;padding: 0 0 2px; color: #000000">Hi <?php echo $userName ?>, forgot your password? Don't worry, we've got your back.</td>
                            </tr>
                            <tr>
                              <td style="margin: 0;padding: 0 0 20px; color: #000000">Please click the button below to set a new password.</td>
                            </tr>
                          </table>
                          <table width="100%" cellpadding="0" cellspacing="0" style="margin: 0;padding: 0;box-sizing: border-box;">
                            <tr>
                              <td style="margin: 0;padding: 0 0 20px;"><a href="<?php echo $link ;?>"><img style="width:25%;" src="<?php echo getConfig('base_domain_path').getConfig('static_path_image')."/reset.png"?>" /></td>

                            </tr>
                            <tr>
                            </tr>
                            <tr>
                              <td style="margin: 0;padding: 0 0 2px; color: #000000">Thanks,</td>
                            </tr>
                            <tr>
                              <td style="color: #000000;">Straz Race Team</td>
                            </tr>
                            <tr>
                              <td style="margin: 0;padding: 0 0 20px;box-sizing: border-box;font-size: 12px;vertical-align: top;text-align: center;">
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </div>
          </td>
        </tr>
      </table>
    </body>
  </html>
