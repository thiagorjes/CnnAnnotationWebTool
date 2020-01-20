<?php
$dimensao=1000;
//lista de imagens
include_once("lista.php");
//lista de classes de interesses
include_once("classes.php");
$classe_selecionada = (isset($_POST['add_box'])?$_POST['add_box']:0);
$idx=0;

if(isset($_POST['idx'])){
  $idx=$_POST['idx'];
}

if(isset($_POST['comando']) && $_POST['comando']=="volta"){
  $idx--;
  $idx=($idx<0?0:$idx);
  $file = Imagens($idx);
  list($img_w, $img_h, $type, $attr) = getimagesize("/var/www/html/".$file);
  $ratio=($img_w/$img_h);
  if($img_w>$img_h){
    $w=$dimensao;
    $h=($dimensao/($ratio));
  }
  else{
    $h=$dimensao;
    $w=($dimensao*($ratio));
  }
  $boxes = Labels($idx);
}
else if( isset($_POST['comando']) && $_POST['comando']=="vai"){
  $idx++;
  $file = Imagens($idx);
  list($img_w, $img_h, $type, $attr) = getimagesize("/var/www/html/".$file);
  $ratio=($img_w/$img_h);
  if($img_w>$img_h){
    $w=$dimensao."px";
    $h=($dimensao/($ratio))+"px";
  }
  else{
    $h=$dimensao;
    $w=($dimensao*($ratio));
  }
  $boxes = Labels($idx);
}
else { 
  $file = Imagens($idx);
  list($img_w, $img_h, $type, $attr) = getimagesize("/var/www/html/".$file);
  $ratio=($img_w/$img_h);
  if($img_w>$img_h){
    $w=$dimensao;
    $h=($dimensao/($ratio));
  }
  else{
    $h=$dimensao;
    $w=($dimensao*($ratio));
  }
  $boxes = Labels($idx);
  if($_POST['novos']=="novidades")
  {
    $fp = fopen("/var/www/html".$boxes, 'w');
    foreach($_POST as $key=>$value){
      if(strpos($key, 'hbox_') !== false){
        list($class,$left,$top,$width,$height) = explode(" ",$value);
        if($width>0 && $height>0){
          fwrite($fp,$class." ".($left+$width/2)/$w." ".($top+$height/2)/$h." ".($width/$w)." ".($height/$h)."\n");
        }
      }
    }
    fclose($fp);
  }
}


$linhas = file("http://localhost/".$boxes);
$linhas = array_unique($linhas);

?>
<html>
  <head>
    <style>
      .vlinha{
        width:0px;
        height:<?php echo $dimensao;?>px;
        top:0px;
        left:0px;
        position: absolute;
        background: transparent;
        border: 1px solid black;
      }
      .hlinha{
        height:0px;
        width:<?php echo $dimensao;?>px;
        left:0px;
        top:0px;
        position: absolute;
        background: transparent;
        border: 1px solid black;
      }
      .eliminar {
        margin: -13px 0px;
        width: 24px;
        height: 23px;
        background-color: red;
        border: 1px solid gray;
        border-radius: 5px;
        text-align: center;
        display: inline-block;
        font-size: 16px;
      }
      body {
        margin:0px;
      }
      .dados{
        right:0px;
        top:0px;
        position:absolute;
      }
      .clicavel{
        background: transparent; 
        top:0px; 
        left:0px;
        position:absolute;
        z-index:999999;
        width:<?php echo $dimensao;?>px;
        height:<?php echo $dimensao;?>px;
      }
      .imagem{
        border:1px solid black;
        width:<?php echo $dimensao;?>px;
        height:<?php echo $dimensao;?>px;
      }
      img {
        width: <?php echo "$w";?>px;
        height: <?php echo "$h";?>px;
      }
     <?php
      foreach($classes as $key=>$value){
        echo ".class_".$value[0]." {
          border: 1px solid ".$value[1].";
        }";
      }
      ?>
      .highlighted {
        border:2px solid white !important;
      }
    </style>
  </head>
  <body id="corpo" >
  <?php 
    $var=0;
    foreach($linhas as $box)
    {
      $var ++;
      $argumentos = explode(" ",$box);
      $class_id = $argumentos[0];
      $x_center = $argumentos[1];
      $y_center = $argumentos[2];
      $width = $argumentos[3];
      $height = $argumentos[4];
      $top = (floatval($y_center) - (floatval($height)/2))*floatval($h);
      $left = (floatval($x_center) - (floatval($width)/2))*floatval($w);
      $width = floatval($width)*floatval($w);
      $height = floatval($height)*floatval($h);
      echo 
      "<div id='box_$var' class='box class_".$classes[$class_id][0]."' style='position:absolute; top:".$top."px; left:".$left."px;width:".$width."px;height:".$height."px;'></div>";
    }
  ?> 
    <!-- <div onclick="showCoords(event)" style="background: transparent; top:0px; left:0px; width:<?php echo $img_w;?>px;height:<?php echo $img_h;?>px;position:absolute;z-index:999999;" ></div> -->
    <div class="clicavel" onclick="showCoords(event)" onmousemove="linhas()"></div>
    <div class="vlinha"></div>
    <div class="hlinha"></div>
    <div class="imagem" id="imagem" >
      <img id='foto' src='<?php echo $file; ?>' />
    </div>  
    <div class="dados" > 
      <input type="text" value="" id="ativa"></input> <?php echo "[".$idx."]:".basename($file);?>
      <form action='index.php' method='post' id='formulario'>
        <input type="hidden" value='false' name="novos" id="novos" />
        <input type="text" value='<?php echo $idx; ?>' name="idx" id="idx" />
        <input type="submit" value="volta" name="comando" id="anterior"></input>
        <input type="submit" value="vai" name="comando" id="proxima"></input>
        <select name="add_box" id="add_box">
        <?php 
          foreach($classes as $key=>$value){
            echo "<option  value=\"".$key."\" ".($key==$classe_selecionada?"selected":"")."  > add ".$value[0]."</option>";
          }
        ?>
        </select>
        <div class="boxes" id="boxes" style="width:450px;display:block;">
        <?php 
          //echo $_POST['novos'];
          $var=0;
          foreach($linhas as $box)
          {
            $var++;
            $argumentos = explode(" ",$box);
            $class_id = $argumentos[0];
            $x_center = $argumentos[1];
            $y_center = $argumentos[2];
            $width = $argumentos[3];
            $height = $argumentos[4];
            $top = (floatval($y_center) - (floatval($height)/2))*floatval($h);
            $left = (floatval($x_center) - (floatval($width)/2))*floatval($w);
            $width = floatval($width)*floatval($w);
            $height = floatval($height)*floatval($h);
            echo "  <div  >
            <input  type='hidden' name='hbox_$var' id='hbox_$var' value='$class_id $left $top $width $height'/>";
            echo "  <input type='text' onmouseover=\"highlight_box('box_$var')\" onclick=\"ativa($var)\" name='box_class_$var' id='box_class_$var' value='$class_id' style='width:30px;display:inline-block;'/>";
            echo "  <input type='text' onmouseover=\"highlight_box('box_$var')\" onclick=\"ativa($var)\" name='box_top_$var' id='box_top_$var' value='$top' style='width:70px;display:inline-block;'/>";
            echo "  <input type='text' onmouseover=\"highlight_box('box_$var')\" onclick=\"ativa($var)\" name='box_left_$var' id='box_left_$var' value='$left' style='width:70px;display:inline-block;'/>";
            echo "  <input type='text' onmouseover=\"highlight_box('box_$var')\" onclick=\"ativa($var)\" name='box_width_$var' id='box_width_$var' value='$width' style='width:70px;display:inline-block;'/>";
            echo "  <input type='text' onmouseover=\"highlight_box('box_$var')\" onclick=\"ativa($var)\" name='box_height_$var' id='box_height_$var' value='$height' style='width:70px;display:inline-block;'/>";
            echo "  <div class='eliminar' onclick='eliminar(\"$var\")'>x</div>";
            echo "</div>";
            
          }
        ?>
        </div>
      </form>
    </div>
  </body>

  <script>   
    function checkKey(e) {
      var event = window.event ? window.event : e;
      console.log(event.keyCode)
      switch (event.keyCode){
        case 37:
          document.getElementById('idx').value=(document.getElementById('idx').value>1?parseInt(document.getElementById('idx').value)-1:0);
          document.getElementById('formulario').submit()
          break;
        case 39:
          document.getElementById('idx').value=parseInt(document.getElementById('idx').value)+1;
          document.getElementById('formulario').submit()
          break;
          
        case 46:
          var selecionado = document.querySelectorAll('.highlighted')
          selecionado.forEach((e)=>{
            let indice = e.id.substring(4)
            eliminar(indice)
          })
          break;
        case 48:
          document.getElementById('add_box').selectedIndex = 0
          break;
        <?php 
        foreach($classes as $key=>$value){
          $valor = 48+$key;
          echo "case $valor:
            document.getElementById('add_box').selectedIndex = $key
            break;";
        }
        ?>
      }
    }
    document.onkeydown = checkKey;
    function linhas(){
      var vlinha=document.querySelectorAll('.vlinha')
      var hlinha=document.querySelectorAll('.hlinha')
      vlinha.forEach( (e)=>{e.style['left']=event.clientX-1})
      hlinha.forEach( (e)=>{e.style['top']=event.clientY-1})
    }
    function eliminar(id){
      ativa(id)
      document.getElementById('box_width_'+id).value=0
      document.getElementById('box_height_'+id).value=0
      document.getElementById('novos').value="novidades"
      document.getElementById('formulario').submit()
    }
    function highlight_box(id){
      var boxes = document.querySelectorAll('.box');
      boxes.forEach(trocaClasse);
      document.getElementById(id).classList.add('highlighted');
    }
    function trocaClasse(elemento){
      elemento.classList.remove('highlighted');
    }
    function showCoords(event){
      if(document.getElementById('ativa').value=='')
      {
        addbox(document.getElementById('add_box').value)   
      }
      var x = event.clientX-1;
      var y = event.clientY-1;
      if( document.getElementById('ativa').value > 0){
        idx = document.getElementById('ativa').value
        if(document.getElementById('hbox_'+idx).value.split(" ").length - 1 == 3){
          //coloca x e y como width e height
          btop=document.getElementById('box_top_'+idx).value
          bleft=document.getElementById('box_left_'+idx).value
          bwidth=Math.abs(x-bleft)
          bheight=Math.abs(y-btop)
          btop=document.getElementById('box_top_'+idx).value>y?y:document.getElementById('box_top_'+idx).value
          bleft=document.getElementById('box_left_'+idx).value>x?x:document.getElementById('box_left_'+idx).value
          document.getElementById('box_top_'+idx).value=btop
          document.getElementById('box_left_'+idx).value=bleft
          document.getElementById('box_width_'+idx).value = bwidth
          document.getElementById('box_height_'+idx).value = bheight
          document.getElementById('hbox_'+idx).value =document.getElementById('hbox_'+idx).value.split(" ")[0]+' '+bleft+' '+btop+' '+bwidth+' '+bheight+' '
        }
        if(document.getElementById('hbox_'+idx).value.split(" ").length - 1 == 1){
          //coloca x e y como top e left
          document.getElementById('box_top_'+idx).value = y
          document.getElementById('box_left_'+idx).value = x
          document.getElementById('hbox_'+idx).value += x+' '+y+' '
        }
        if(document.getElementById('hbox_'+idx).value.split(" ").length - 1 == 5){
          $valores = document.getElementById('hbox_'+idx).value.split(" ")
          classe = $valores[0]
          bleft = $valores[1]
          btop = $valores[2]
          bwidth = $valores[3]
          bheight = $valores[4]

          if(document.getElementById('box_'+idx) == null){
            console.log("novo")
            myHTML = document.createElement("div")
            myHTML.classList.add('box')
            myHTML.classList.add('class_'+classe)
            myHTML.id='box_'+idx
            myHTML.style="position:absolute; top:"+btop+"px; left:"+bleft+"px;width:"+bwidth+"px;height:"+bheight+"px;"
            //HTML="<div id='box_"+idx+"' class='box class_"+classe+"' style='position:absolute; top:"+btop+"px; left:"+bleft+"px;width:"+bwidth+"px;height:"+bheight+"px;'></div>"
            document.getElementById('corpo').appendChild(myHTML)
          }
          else {
            console.log("existente")
            document.getElementById('box_'+idx).style["position"]="absolute"
            document.getElementById('box_'+idx).style["top"]= btop+"px"
            document.getElementById('box_'+idx).style["left"]= bleft+"px"
            document.getElementById('box_'+idx).style["width"]= bwidth+"px"
            document.getElementById('box_'+idx).style["height"]= bheight+"px"
          }
          document.getElementById('novos').value="novidades"
          document.getElementById('formulario').submit()
        }
      }
    }
    function ativa(idx){
      document.getElementById('ativa').value=idx
      if(document.getElementById('hbox_'+idx))
      {
        document.getElementById('hbox_'+idx).value=document.getElementById('box_class_'+idx).value+' '
      }
    }
    function addbox(classe){
      let total=(document.getElementById('boxes').childNodes.length)-Math.floor(document.getElementById('boxes').childNodes.length/2)
      newHTML=document.createElement("div")
      newHTML.onclick=function () {ativa(total)}
      newHTML.innerHTML='<input type="hidden" name="hbox_'+total+'" id="hbox_'+total+'" value="'+classe+' ">\
      <input type="text" name="box_class_'+total+'" id="box_class_'+total+'" onclick="highlight_box(\'box_'+total+'\')" value="'+classe+'" style="width:30px;display:inline-block;">  \
      <input type="text" name="box_top_'+total+'" id="box_top_'+total+'" value="" style="width:70px;display:inline-block;">\
      <input type="text" name="box_left_'+total+'"  id="box_left_'+total+'" value="" style="width:70px;display:inline-block;">\
      <input type="text" name="box_width_'+total+'" id="box_width_'+total+'" value="" style="width:70px;display:inline-block;">\
      <input type="text" name="box_height_'+total+'" id="box_height_'+total+'" value="" style="width:70px;display:inline-block;">\
      <div class=\'eliminar\' onclick=\'eliminar("'+total+'")\'>x</div>' 
      document.getElementById('boxes').appendChild(newHTML)
      ativa(total)
    }
  </script>
</html>