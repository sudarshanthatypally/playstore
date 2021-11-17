<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta property="description" content="Friendly Battle Invite: Epiko Regal" />
    <meta name="description" content="Open link in Epiko Regal or download the game" />
    <meta property="og:title" content="Epiko Regal: The Kingdom of Epic Legends" />
    <meta property="og:description" content="Open link in Epiko Regal or download the game" />
    <meta name="keywords" content="Friendly Battle Invite: Epiko Regal" />

    <meta property="og:image" content="https://epiko-regal-meta-image.web.app/assets/img/meta/Epiko-Regal-The-Kingdom-of-Epic-Legends.png" />
    <meta name="apple-mobile-web-app-title" content="Epiko Regal: The Kingdom of Epic Legends" />
    <meta name="application-name" content="Epiko Regal: The Kingdom of Epic Legends" />
    <?php echo autoload::htmlHeaders(); ?>
    <?php echo autoload::stylesheetFiles(); ?>
    <?php echo autoload::javascriptFiles(); ?>
  </head>
  <body>
    <?php echo page::renderComponent('pageContent','pageContentHeader'); ?>
    <?php echo $aadya_contents; ?>
    <?php echo page::renderTemplate('pageContent','pageContentFooter'); ?>
  </body>
</html>
