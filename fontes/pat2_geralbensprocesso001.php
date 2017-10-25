<?
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2013  DBselller Servicos de Informatica             
 *                            www.dbseller.com.br                     
 *                         e-cidade@dbseller.com.br                   
 *                                                                    
 *  Este programa e software livre; voce pode redistribui-lo e/ou     
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme  
 *  publicada pela Free Software Foundation; tanto a versao 2 da      
 *  Licenca como (a seu criterio) qualquer versao mais nova.          
 *                                                                    
 *  Este programa e distribuido na expectativa de ser util, mas SEM   
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de              
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM           
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais  
 *  detalhes.                                                         
 *                                                                    
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU     
 *  junto com este programa; se nao, escreva para a Free Software     
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA          
 *  02111-1307, USA.                                                  
 *  
 *  Copia da licenca no diretorio licenca/licenca_en.txt 
 *                                licenca/licenca_pt.txt 
 */

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta_plugin.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_bens_classe.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("classes/db_cfpatri_classe.php"));
require_once(modification("classes/db_db_depart_classe.php"));
require_once(modification("classes/db_departdiv_classe.php"));
require_once(modification("libs/db_app.utils.php"));

$clrotulo 			= new rotulocampo;
$cldb_depart		= new cl_db_depart;
$clcfpatric 		= new cl_cfpatri;
$clbens				= new cl_bens;
$cldepartdiv 		= new cl_departdiv;
$aux_orgao 			= new cl_arquivo_auxiliar;
$aux_unidade 		= new cl_arquivo_auxiliar;
$aux 				= new cl_arquivo_auxiliar;

$clbens->rotulo->label();
$cldb_depart->rotulo->label();

db_postmemory($HTTP_POST_VARS);

//Verifica se utiliza pesquisa por orgão sim ou não
$t06_pesqorgao = "f";
$resPesquisaOrgao	= $clcfpatric->sql_record($clcfpatric->sql_query_file(null,'t06_pesqorgao'));
if($clcfpatric->numrows > 0) {
	db_fieldsmemory($resPesquisaOrgao,0);
}
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<?php 
db_app::load('scripts.js');
db_app::load('prototype.js');
db_app::load('estilos.css');
?>
</head>
<body bgcolor=#CCCCCC>
<form class="container" name="form1" method="post" action="">
<fieldset>
<legend>Relatórios - Bens pro Processo/Fornecedor</legend>
<table width='100%'>
   <tr>
     <td width='100%' colspan="2">
      <table id="tbOrgaos" width="100%">
    	<?
        // $aux = new cl_arquivo_auxiliar;
        $aux_orgao->cabecalho = "<strong>Órgãos</strong>";
        $aux_orgao->codigo = "o40_orgao"; //chave de retorno da func
        $aux_orgao->descr  = "o40_descr";   //chave de retorno
        $aux_orgao->nomeobjeto = 'orgaos';
        $aux_orgao->funcao_js = 'js_mostra_org';
        $aux_orgao->funcao_js_hide = 'js_mostra_org1';
        $aux_orgao->sql_exec  = "";
        $aux_orgao->func_arquivo = "func_orcorgao.php";  //func a executar
        $aux_orgao->nomeiframe = "db_iframe_orcorgao";
        $aux_orgao->localjan = "";
        $aux_orgao->onclick = "";
        $aux_orgao->db_opcao = 2;
        $aux_orgao->tipo = 2;
        $aux_orgao->top = 0;
        $aux_orgao->linhas = 4;
        $aux_orgao->vwidth = 400;
        $aux_orgao->nome_botao = 'db_lanca_orgao';
        $aux_orgao->funcao_gera_formulario();
      	?>
      </table>
     </td>
   </tr>
   <tr>
     <td width='100%' colspan="2">
      <table id="tbUnidades" width="100%">
     	<?
         // $aux = new cl_arquivo_auxiliar;
         $aux_unidade->cabecalho = "<strong>Unidades</strong>";
         $aux_unidade->codigo = "o41_unidade"; //chave de retorno da func
         $aux_unidade->descr  = "o41_descr";   //chave de retorno
         $aux_unidade->nomeobjeto = 'unidades';
         $aux_unidade->funcao_js = 'js_mostra_uni';
         $aux_unidade->funcao_js_hide = 'js_mostra_uni1';
         $aux_unidade->sql_exec  = "";
         $aux_unidade->func_arquivo = "func_orcunidade.php";  //func a executar
         $aux_unidade->nomeiframe = "db_iframe_orcunidade";
         $aux_unidade->localjan = "";
         $aux_unidade->onclick = "";
         $aux_unidade->db_opcao = 2;
         $aux_unidade->tipo = 2;
         $aux_unidade->top = 0;
         $aux_unidade->linhas = 4;
         $aux_unidade->vwidth = 400;
         $aux_unidade->nome_botao = 'db_lanca_unidade';   
         $aux_unidade->funcao_gera_formulario();
       	?>
      </table>
     </td>
   </tr>
   <tr>
     <td width='100%' colspan="2">
      <table id="tbDepartamentos" width="100%">
     	 <?
         // $aux = new cl_arquivo_auxiliar;
         $aux->cabecalho = "<strong>Departamentos</strong>";
         $aux->codigo = "coddepto"; //chave de retorno da func
         $aux->descr  = "descrdepto";   //chave de retorno
         $aux->nomeobjeto = 'departamentos';
         $aux->funcao_js = 'js_mostra';
         $aux->funcao_js_hide = 'js_mostra1';
         $aux->sql_exec  = "";
         $aux->func_arquivo = "func_db_depart.php";  //func a executar
         $aux->nomeiframe = "db_iframe_db_depart";
         $aux->localjan = "";
         $aux->onclick = "";
         $aux->db_opcao = 2;
         $aux->tipo = 2;
         $aux->top = 0;
         $aux->linhas = 4;
         $aux->vwidth = 400;
         $aux->nome_botao = 'db_lanca_departamento';
         $aux->funcao_gera_formulario();
       	?>
      </table>
     </td>
   </tr>
   <tr>
     <td><b>Aquisição em:</b></td>
     <td nowrap>
     <? db_inputdata("data_inicial","","","",true,"text",4);?>&nbsp;<b>a</b>&nbsp;<?db_inputdata("data_final","","","",true,"text",4);?>
     </td>
   </tr>
  </table>
</fieldset>
<input type="button" value="Emitir relatório" onClick="js_emite();">
</form>
<?
  db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script>
function js_emite(){
   var query        = "";
   var data_inicial = $F("data_inicial");
   var data_final   = $F("data_final");

   if (data_inicial != "" || data_final != ""){
        if (data_inicial != ""){
             var vet_data_inicial = data_inicial.split("/");
             data_inicial         = vet_data_inicial[2]+"-"+vet_data_inicial[1]+"-"+vet_data_inicial[0];
        }
        if (data_final != ""){
             var vet_data_final   = data_final.split("/");
             data_final           = vet_data_final[2]+"-"+vet_data_final[1]+"-"+vet_data_final[0];
        }

        if (data_inicial != "" && data_final != ""){
             if (data_inicial > data_final){
                  alert(_M("patrimonial.patrimonio.pat2_geralbens001.data_inicial_maior_data_final"));
                  return false;
             }
        }
   
        query = "data_inicial="+data_inicial+"&data_final="+data_final+"&";
   } 
   
   if($('orgaos')){
	    //Le os itens lançados na combo do orgao
			vir="";
		 	listaorgaos="";
		 
		 	for(x=0;x<document.form1.orgaos.length;x++){
		  	listaorgaos+=vir+document.form1.orgaos.options[x].value;
		  	vir=",";
		 	}
			if(listaorgaos!=""){ 	
				query +='&orgaos=('+listaorgaos+')';
			} else {
				query +='&orgaos=';
			}
		}
		
		//Le os itens lançados na combo da unidade
		if($('unidades')){
			vir="";
	 		listaunidades="";
	 
		 	for(x=0;x<document.form1.unidades.length;x++){
		  	listaunidades+=vir+document.form1.unidades.options[x].value;
		  	vir=",";
		 	} 
		 	if(listaunidades!=""){ 	
				query +='&unidades=('+listaunidades+')';
			} else {
				query +='&unidades=';
			}
		 	
		}
		
	 	//Le os itens lançados na combo do orgao
	 	if($('departamentos')){	
			vir="";
		 	listadepartamentos="";
		 
		 	for(x=0;x<document.form1.departamentos.length;x++){
		  	listadepartamentos+=vir+document.form1.departamentos.options[x].value;
		  	vir=",";
		 	} 
		 	if(listadepartamentos!=""){ 	
				query +='&departamentos=('+listadepartamentos+')';
			} else {
				query +='&departamentos=';
			}
		 	
   	}
   
   sUrl = "pat2_geralbensprocesso002.php";
   jan = window.open(sUrl+'?'+query,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
   jan.moveTo(0,0);
}
//Reescrevendo a função de busca do iframe lança unidades
function js_BuscaDadosArquivounidades(chave){
	
	query="";
 	vir="";
 	listaorgaos="";
 
 	for(x=0;x<document.form1.orgaos.length;x++){
  	listaorgaos+=vir+document.form1.orgaos.options[x].value;
  	vir=",";
 	} 
 	if(listaorgaos!=""){
 		query +='&orgaos=('+listaorgaos+')';
 	}
 	
  document.form1.db_lanca_unidade.onclick = '';
  if(chave){
  	js_OpenJanelaIframe('','db_iframe_orcunidade','func_orcunidade.php?funcao_js=parent.js_mostra_uni|o41_unidade|o41_descr'+query,'Pesquisa',true);
  }else{
  	
    js_OpenJanelaIframe('','db_iframe_orcunidade','func_orcunidade.php?pesquisa_chave='+document.form1.o41_unidade.value+'&funcao_js=parent.js_mostra_uni1'+query,'Pesquisa',false);
  }
}
//Reescrevendo a função de busca do iframe lança departamentos
function js_BuscaDadosArquivodepartamentos(chave){
	
	query="";
 	vir="";
 	listaunidades="";
 
 	for(x=0;x<document.form1.unidades.length;x++){
  	listaunidades+=vir+document.form1.unidades.options[x].value;
  	vir=",";
 	} 
	
	vir= "";
	listaorgaos = "";
	for(x=0;x<document.form1.orgaos.length;x++){
  	listaorgaos+=vir+document.form1.orgaos.options[x].value;
  	vir=",";
 	} 
	if (listaunidades.length > 0){ 
 	  query += '&unidades=('+listaunidades+')';
	}
	if (listaorgaos.length > 0)	{
 	  query +='&orgao='+listaorgaos;
	}
 
  document.form1.db_lanca_departamento.onclick = '';
  if(chave){
    js_OpenJanelaIframe('','db_iframe_db_depart','func_db_depart.php?funcao_js=parent.js_mostra|coddepto|descrdepto'+query,'Pesquisa',true);
  }else{
    js_OpenJanelaIframe('','db_iframe_db_depart','func_db_depart.php?pesquisa_chave='+document.form1.coddepto.value+'&funcao_js=parent.js_mostra1'+query,'Pesquisa',false);
  }
}
</script>