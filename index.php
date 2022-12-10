<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Slide show</title>
  <link rel="stylesheet" href="base.css">
</head>
<body>
<?php
function scan_dir($dir) {
  $ignored = array('.', '..', '.svn', '.htaccess');

  $files = array();    
  foreach (scandir($dir) as $file) {
      if (in_array($file, $ignored)) continue;
      $files[$file] = filemtime($dir . '/' . $file);
  }

  arsort($files);
  $files = array_keys($files);

  return ($files) ? $files : false;
}
if(!isset($_GET['dir'])){
  echo '<ul>';
  $dir    = './';
  $files = array_filter(scan_dir($dir),"is_dir");
  foreach($files as $f) {
    if (!preg_match("/^\./",$f)) {
      $childfiles = scandir($f);
      $count = 0;
      foreach($childfiles as $c) {
        if (preg_match("/image|video/",mime_content_type($f.'/'.$c))){
          $count++;
        };
      }
      echo '<li><a href="index.php?dir='.$f.'">'.$f.' ['.$count.']</li>';
    }
  }
  echo '</ul>';
} else {
  if (preg_match('/^\.|\/+|~/',$_GET['dir'])){
    die('only direct child folders');
  }
  $all = array();
  $dir = './'.$_GET['dir'];
  if (!is_dir($dir)) {
    die('Cannot find the folder, soz…');
  }
  $files = scandir($dir);
  foreach($files as $f) {
    if (preg_match("/image|video/",mime_content_type($dir.'/'.$f))){
      array_push($all,$f);
    }
  }
?>
<div id="slideshow-container"></div>
<script>
  let slideshow = {
    container: '#slideshow-container',
    media: <?php echo json_encode($all);?>,
    folder: '<?php echo urlencode(str_replace('index.php?','',$_GET['dir']))?>/',
    autoplay: 'yes'
  }
</script>
<?php  
echo '<script src="slideshow.js?v=' . rand(1,10) . '"></script>';
?>
<?php } ?>
</body>
</html>