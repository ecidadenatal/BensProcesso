<?
/*
 * E-cidade Software Publico para Gestao Municipal
 * Copyright (C) 2014 DBSeller Servicos de Informatica
 * www.dbseller.com.br
 * e-cidade@dbseller.com.br
 *
 * Este programa e software livre; voce pode redistribui-lo e/ou
 * modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 * publicada pela Free Software Foundation; tanto a versao 2 da
 * Licenca como (a seu criterio) qualquer versao mais nova.
 *
 * Este programa e distribuido na expectativa de ser util, mas SEM
 * QUALQUER GARANTIA; sem mesmo a garantia implicita de
 * COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 * PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 * detalhes.
 *
 * Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 * junto com este programa; se nao, escreva para a Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307, USA.
 *
 * Copia da licenca no diretorio licenca/licenca_en.txt
 * licenca/licenca_pt.txt
 */
require_once ("fpdf151/pdf.php");
require_once ("libs/db_sql.php");
require_once ("classes/db_bens_classe.php");
require_once ("classes/db_bensbaix_classe.php");
require_once ("classes/db_cfpatriplaca_classe.php");

$clbens = new cl_bens ();
$clbensbaix = new cl_bensbaix ();
$clcfpatriplaca = new cl_cfpatriplaca ();

parse_str ( $HTTP_SERVER_VARS ['QUERY_STRING'] );

$sSqlPatriPlaca = $clcfpatriplaca->sql_query_file ( db_getsession ( "DB_instit" ) );
$res_cfpatriplaca = $clcfpatriplaca->sql_record ( $sSqlPatriPlaca );
if ($clcfpatriplaca->numrows > 0) {
	db_fieldsmemory ( $res_cfpatriplaca, 0 );
} else {
	
	$sMsg = _M ( 'patrimonial.patrimonio.pat2_geralbens002.nao_existem_placas_para_instituicao' );
	db_redireciona ( 'db_erros.php?fechar=true&db_erro=' . $sMsg );
	exit ();
}

$sWhere = "";

$flag_datas = 0;
if (isset ( $data_inicial ) && trim ( @$data_inicial ) != "" && isset ( $data_final ) && trim ( @$data_final ) != "") {
	$flag_datas = 1;
} else if (isset ( $data_inicial ) && trim ( @$data_inicial ) != "") {
	$flag_datas = 2;
} else if (isset ( $data_final ) && trim ( @$data_final ) != "") {
	$flag_datas = 3;
}

if (($flag_datas == 1 || $flag_datas == 2 || $flag_datas == 3) && $sWhere != "") {
	$sWhere .= " and ";
}

if ($flag_datas == 1) {
	$sWhere .= "t52_dtaqu between '$data_inicial' and '$data_final'";
	$info .= "\nPeriodo de " . db_formatar ( $data_inicial, "d" ) . " a " . db_formatar ( $data_final, "d" );
}

if ($flag_datas == 2) {
	$sWhere .= "t52_dtaqu >= '$data_inicial'";
	$info .= "\nAquisição a partir de " . db_formatar ( $data_inicial, "d" );
}

if ($flag_datas == 3) {
	$sWhere .= "t52_dtaqu <= '$data_final'";
	$info .= "\nAquisição até " . db_formatar ( $data_final, "d" );
}

$flag_classi = false;

if ($imp_classi == "S") {
	$flag_classi = true;
}

$head3 = "RELATÓRIO GERAL DE BENS";
$head4 = $info;

if ($q_pagina == 'S') {
	$head5 = "";
} elseif ($q_pagina == 'orgao') {
	$head5 = "Quebra por Órgão ";
} else if ($q_pagina == 'unidade') {
	$head5 = "Quebra por Unidade ";
} else if ($q_pagina == 'departamento') {
	$head5 = "Quebra por Departamento ";
}

if ($sWhere != "") {
	$sWhere .= " and ";
}

$sWhere .= "t52_instit = " . db_getsession ( "DB_instit" );

if ($t07_confplaca == 1 or $t07_confplaca == 4) {
	$campos = "t52_bem, t52_descr, round(t52_valaqu,2) as t52_valaqu, t52_dtaqu, cast( regexp_replace( coalesce(nullif(trim(t52_ident),''), '0') , '[^0-9.,-]' , '', 'g') as numeric) as t52_ident,
  					 t52_depart, descrdepto, t52_numcgm, z01_nome, t52_obs, t64_class, t64_descr, t33_divisao, departdiv.t30_descr,
  					 (select count(*)
                     from bensplaca
                          inner join bensplacaimpressa on bensplacaimpressa.t73_bensplaca = bensplaca.t41_codigo
                    where t41_bem = t52_bem) as totaletiquetas, ";
} else {
	$campos = "t52_bem, t52_descr, round(t52_valaqu,2) as t52_valaqu, t52_dtaqu, t52_ident, t52_depart, descrdepto, t52_numcgm, z01_nome, t52_obs,
  					 t64_class, t64_descr, t33_divisao, departdiv.t30_descr,
  					 (select count(*)
                     from bensplaca
                          inner join bensplacaimpressa on bensplacaimpressa.t73_bensplaca = bensplaca.t41_codigo
                    where t41_bem = t52_bem) as totaletiquetas, ";
}

$campos .= "(select t53_ntfisc 
		       from bensmater 
	          where t53_codbem = t52_bem) as nota_fiscal,
		    (select processo from plugins.bensprocesso where bem = t52_bem) as processo";
$campos .= ",o40_descr,o40_orgao,o41_unidade,o41_descr,db01_orgao,db01_unidade";
$campos = "distinct " . $campos;
if ($orgaos != "") {
	$sWhere .= " and db01_orgao in $orgaos ";
}
if ($unidades != "") {
	$sWhere .= " and db01_unidade in $unidades ";
}
if ($departamentos != "") {
	$sWhere .= " and db01_coddepto in $departamentos ";
}
$sWhere .= " and db01_anousu =" . db_getsession ( 'DB_anousu' );

$sqlrelatorio = $clbens->sql_query_orgao ( null, "$campos", "t52_depart, processo, t52_numcgm, t52_descr", "$sWhere" );
$result = $clbens->sql_record ( $sqlrelatorio );
if ($clbens->numrows == 0) {
	
	$sMsg = _M ( 'patrimonial.patrimonio.pat2_geralbens002.nao_existem_registros' );
	db_redireciona ( 'db_erros.php?fechar=true&db_erro=' . $sMsg );
	exit ();
}

$pdf = new PDF ( "L" );
$pdf->Open ();
$pdf->AliasNbPages ();
$total = 0;
$pdf->setfillcolor ( 235 );
$pdf->setfont ( 'arial', 'b', 8 );
$pdf->AddPage ( "L" );

$numrows = $clbens->numrows;

// quebra de página
$background = 1;
$sIdOrgaoUnidade = "";

$iQtdTotalBem = 0;
$nValotTotalBem = 0;

$sHashOrgaoUnidade = "";
$sHashProcessoFornecedor = "";
$sHashItemProcesso = "";

for($x = 0; $x < $numrows; $x ++) {
	
	db_fieldsmemory ( $result, $x );
	
	$sWhereBensBaixados = " t55_codbem = {$t52_bem}  ";
	if (! empty ( $data_final )) {
		$sWhereBensBaixados .= " and t55_baixa <= '{$data_final}' ";
	}
	$sSqlBuscaBensBaixados = $clbensbaix->sql_query_file ( null, "*", null, $sWhereBensBaixados );
	$result_bensbaix = $clbensbaix->sql_record ( $sSqlBuscaBensBaixados );
	if ($clbensbaix->numrows > 0) {
		continue;
	}
	
	$sSqlDadosBens = "select count(*) as qtd, 
			                 sum(t52_valaqu) as vlrtotal
			            from bens 
			                 left join plugins.bensprocesso on bensprocesso.bem = bens.t52_bem
			           where trim(t52_descr) = trim('{$t52_descr}') 
			             and t52_numcgm = {$t52_numcgm} 
			             and t52_depart = {$t52_depart}
			             and case when bensprocesso.bem is not null then trim(processo) = trim('{$processo}') else true end;";
	$rsDadosBens = db_query($sSqlDadosBens);
	$oDadosTotaisBens = db_utils::fieldsMemory($rsDadosBens, 0);
	
	/*Quebra Orgao/Unidade */
	$sIdOrgaoUnidade = $o40_orgao."|".$o41_unidade;
	if ($sHashOrgaoUnidade != $sIdOrgaoUnidade) {

        if ($iUnidade != 0) {
          $pdf->ln(3);
        }

        $pdf->setfont ( 'arial', 'b', 8 );
        $pdf->cell ( 30, 4, "Órgão", 0, 0, "L", 0 );
        $pdf->cell ( 30, 4, $o40_orgao . " - " . $o40_descr, 0, 1, "L", 0 );
        $pdf->setfont ( 'arial', '', 7 );
	
		$pdf->setfont ( 'arial', 'b', 8 );
		$pdf->cell ( 30, 4, "Unidade", 0, 0, "L", 0 );
		$pdf->cell ( 30, 4, $o41_unidade . " - " . $o41_descr, 0, 1, "L", 0 );
		$pdf->setfont ( 'arial', '', 7 );

	}
	$sHashOrgaoUnidade = $sIdOrgaoUnidade;
	/*Fim Quebra Orgao/Unidade */
	
	/* Quebra ProcessoFornecedor */
	$sIdProcessoFornecedor = $processo."|".$t52_numcgm;
	if ( $sHashProcessoFornecedor != $sIdProcessoFornecedor){
	
        $pdf->ln(1);	
		$pdf->setfont ( 'arial', '', 7 );
		$pdf->cell ( 30, 4, "Processo:", 0, 0, "L", 0 );
		$pdf->cell ( 30, 4, $processo, 0, 1, "L", 0 );
		$pdf->cell ( 30, 4, "Fornecedor:", 0, 0, "L", 0 );
		$pdf->cell ( 30, 4, $t52_numcgm." - ".$z01_nome, 0, 1, "L", 0 );

	    $pdf->setfont ( 'arial', '', 7 );
        $pdf->cell ( 80,  4, "Descrição",   "T", 0, "L", 0);
        $pdf->cell ( 22,  4, "Quantidade",  "T", 0, "R", 0);
        $pdf->cell ( 22,  4, "Valor Total", "T", 0, "R", 0);
        $pdf->cell ( 155, 4, "",            "T", 1, "L", 0);
       
        $pdf->cell (  5,  4, "",                     "B", 0, "L", 0); 
        $pdf->cell ( 15,  4, "Aquisição",            "B", 0, "L", 0);
        $pdf->cell ( 22,  4, "Nota Fiscal",          "B", 0, "L", 0);
        $pdf->cell ( 120, 4, "Especificação do Bem", "B", 0, "L", 0);
        $pdf->cell ( 70,  4, "Departamento",         "B", 0, "L", 0);
        $pdf->cell ( 22,  4, "Valor Unitário",       "B", 0, "L", 0);
        $pdf->cell ( 25,  4, "Guia Tombamento",      "B", 1, "L", 0);
        $pdf->setfont ( 'arial', '', 7 );
        
	}
	$sHashProcessoFornecedor = $sIdProcessoFornecedor;
	
	// Imprime os dados
    $sIdItemProcesso = $processo."|".$t52_descr;
	if ($sHashItemProcesso != $sIdItemProcesso) {
      $pdf->setfont ( 'arial', 'b', 7 );
	  $pdf->cell ( 80,  4, substr($t52_descr, 0, 70),   0, 0, "L", 0);
	  $pdf->cell ( 22,  4, $oDadosTotaisBens->qtd,               0, 0, "R", 0);
	  $pdf->cell ( 22,  4, db_formatar ( $oDadosTotaisBens->vlrtotal, "f"),             0, 1, "R", 0);
      $pdf->setfont ( 'arial', '', 7 );
      
      $sHashItemProcesso = $sIdItemProcesso;
	}
	
    $pdf->cell ( 5,  4, '',                    0, 0, "L", 0);
	$pdf->cell ( 15,  4, db_formatar ( $t52_dtaqu, "d" ),                    0, 0, "L", 0);
	$pdf->cell ( 22,  4, $nota_fiscal,                                       0, 0, "L", 0);
	$pdf->cell ( 120, 4, substr ( $t52_obs, 0, 50 ),                         0, 0, "L", 0);
	$pdf->cell ( 70,  4, $t52_depart . "-" . substr ( $descrdepto, 0, 36 ),  0, 0, "L", 0);
	$pdf->cell ( 22,  4, db_formatar ( $t52_valaqu, "f" ),                   0, 0, "L", 0);
	$pdf->cell ( 25,  4, $t52_ident,                                         0, 1, "L", 0);
	
	if ($pdf->GetY() > $pdf->h - 25) {
		
		$pdf->setfont ( 'arial', '', 7 );
		$pdf->cell ( 80,  4, "Descrição",   "T", 0, "L", 0);
		$pdf->cell ( 22,  4, "Quantidade",  "T", 0, "R", 0);
		$pdf->cell ( 22,  4, "Valor Total", "T", 0, "R", 0);
		$pdf->cell ( 155, 4, "",            "T", 1, "L", 0);
		 
		$pdf->cell (  5,  4, "",                     "B", 0, "L", 0);
		$pdf->cell ( 15,  4, "Aquisição",            "B", 0, "L", 0);
		$pdf->cell ( 22,  4, "Nota Fiscal",          "B", 0, "L", 0);
		$pdf->cell ( 120, 4, "Especificação do Bem", "B", 0, "L", 0);
		$pdf->cell ( 70,  4, "Departamento",         "B", 0, "L", 0);
		$pdf->cell ( 22,  4, "Valor Unitário",       "B", 0, "L", 0);
		$pdf->cell ( 25,  4, "Guia Tombamento",      "B", 1, "L", 0);
		$pdf->setfont ( 'arial', '', 7 );
		
	}
	
}
$pdf->Output ();
?>
