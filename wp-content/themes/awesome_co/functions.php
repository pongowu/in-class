<?php 
// don't close this PHP. this page only contains php & runs before the header.php starts
// activate post featured images

add_theme_support('post-thumbnails');

// add custom image sizes

add_image_size('awesome-home-banner', 960, 330, true);

add_image_size('awesome-interior-banner', 960, 113, true);