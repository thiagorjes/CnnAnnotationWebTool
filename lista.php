<?php 

function Imagens($id)
{
  $file = new SplFileObject("imagens.txt");
  $file->seek($id);
  return trim(preg_replace('/\s\s+/', ' ', $file->current()));
}

function Labels($id)
{
  $file = new SplFileObject("labels.txt");
  $file->seek($id);
  return trim(preg_replace('/\s\s+/', ' ', $file->current()));
}


?>