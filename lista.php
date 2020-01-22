<?php 

function Imagens($id)
{
  $file = new SplFileObject("img_cityscapes.txt");
  $file->seek($id);
  return trim(preg_replace('/\s\s+/', ' ', $file->current()));
}

function Labels($id)
{
  $file = new SplFileObject("lbl_cityscapes.txt");
  $file->seek($id);
  return trim(preg_replace('/\s\s+/', ' ', $file->current()));
}


?>
