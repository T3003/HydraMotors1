<?php
/*
$id - Ponteiro da Conex o
$sql - Cl usula SQL a executar
$erro - Especifica se a fun  o exibe ou n o(0=n o, 1=sim)
$res - Resposta
*/
function mysqlexecuta($id,$sql) {
   //if(empty($sql) OR !($id)){
     //  return 0; //Erro na conex o ou no comando SQL
   //}    
   if ($res = mysqli_query($id,$sql)) {
      return $res;
   }
   else{
      echo "Ocorreu um erro na conexao com o banco de dados. Favor Contactar o Administrador.";
      
      exit; 
   }
   //return $res;
   
 }
?>
