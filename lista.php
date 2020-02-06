<?php 

function Imagens($id)
{
  $file = new SplFileObject("img_kitti.txt");
  $file->seek($id);
  return trim(preg_replace('/\s\s+/', ' ', $file->current()));
}

function Labels($id)
{
  $file = new SplFileObject("lbl_kitti.txt");
  $file->seek($id);
  return trim(preg_replace('/\s\s+/', ' ', $file->current()));
}


?>
