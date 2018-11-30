<?php
// Extend the TCPDF class to create custom MultiRow
class RCobroReporteFacturaClienteTodo2 extends ReportePDF {
	var $datos_titulo;
	var $datos_detalle;
	var $ancho_hoja;
	var $gerencia;
	var $numeracion;
	var $ancho_sin_totales;
	var $cantidad_columnas_estaticas;
	var $s1;
	var $s2;
	var $s3;
	var $s4;
	var $s5;
	var $s6;
	
	var $t1;
	var $t2;
	var $t3;
	var $t4;
	var $t5;
	var $t6;
	var $total;
	var $datos_entidad;
	var $datos_periodo;
	var $cant;
	var $valor;
	var $signo="";
	var $a="";
	var $factura;
	
	function datosHeader ($detalle) {
		$this->SetHeaderMargin(10);
		$this->SetAutoPageBreak(TRUE, 10);
		$this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
		$this->datos_detalle = $detalle;
		$this->datos_titulo = $resultado;
		$this->SetMargins(10, 15, 5,10);
	}

	function Header() {		
	}
	//	
	function generarCabecera(){
		$conf_par_tablewidths=array(7,60,15,30,30,30,30);
		$conf_par_tablenumbers=array(0,0,0,0,0,0,0);
		$conf_par_tablealigns=array('C','C','C','C','C','C','C');
		$conf_tabletextcolor=array();
		$this->tablewidths=$conf_par_tablewidths;
		$this->tablealigns=$conf_par_tablealigns;
		$this->tablenumbers=$conf_par_tablenumbers;
		$this->tableborders=$conf_tableborders;
		$this->tabletextcolor=$conf_tabletextcolor;
		$valor=$a;
		$var = $this->objParam->getParametro('tipo_moneda');//MT MA MB
		$RowArray = array(
							's0' => 'Nº',
							's1' => 'Razon Social:',
							's2' => 'Cantidad Facturas:',
							's3' => 'Total Importe de Facturas:',
							's4' => 'Total Importe Cobrado(BS):',
							's5' => 'Total Saldo por Cobrar (BS):',
						);
		$this->MultiRow($RowArray, false, 1);
	}
	//
	function generarReporte($detalle) {
		$this->factura = $detalle->getParameter('factura');
		$this->setFontSubsetting(false);
		$this->AddPage();
		$this->generarCuerpo($this->factura);
	}
	//		
	function generarCuerpo($detalle){		
		//function
		$this->cab();
		$count = 1;
		$sw = 0;
		$ult_region = '';
		$fill = 0;
		$this->total = count($detalle);
		$this->s1 = 0;
		$this->s2 = 0;
		$this->s3 = 0;
		$this->s4 = 0;
		$this->s5 = 0;
		$this->imprimirLinea($val,$count,$fill);
	}
	//desde 
	function imprimirLinea($val,$count,$fill){
		$this->SetFillColor(224, 235, 255);
		$this->SetTextColor(0);
		$this->SetFont('','',6);
		$acreedor=0;
		$var='';
		$sw="0";
		
		$primero='';
		$conteoFactura=0;
		//var_dump($this->factura);
		foreach ($this->factura as $datarow) {
			//var_dump($datarow); 
			$cobroFactura=$datarow['importe_cobro_factura'];
			$saldo = $saldo - $cobroFactura ;				
			if($primero != $datarow['razon_social'] ){
				
			foreach ($this->factura as $conteo){
							if($conteo['razon_social'] == $datarow['razon_social']){
								$conteoFactura++;									
								}
														
						}
				$importeDocTotal=0;
						foreach ($this->factura as $conteo){
							if($conteo['razon_social'] == $datarow['razon_social']){
								$importeDocTotal=$importeDocTotal + $conteo['importe_doc'];								
								}
														
						}
				$importeCobradoTotal=0;
						foreach ($this->factura as $conteo){
							if($conteo['razon_social'] == $datarow['razon_social']){
								$importeCobradoTotal=$importeCobradoTotal + $conteo['importe_cobrado_mb'];								
								}
														
						}
				$importeSaldoTotal=0;
						foreach ($this->factura as $conteo){
							if($conteo['razon_social'] == $datarow['razon_social']){
								$importeSaldoTotal=$importeSaldoTotal+ $conteo['saldo_por_cobrar'];									
							 }
														
						}
			
						$this->tablealigns=array('C','L','R','R','R','R','R');	// las alings de las tablas center left right		
						$this->tableborders=array('RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB','RLTB'); // los bordes de la tabla right left top botton
						$this->tabletextcolor=array();	
						
						$importeDocTotal= ''.(string)(number_format($importeDocTotal, 2, '.', ',')).'';	
						$importeCobradoTotal= ''.(string)(number_format($importeCobradoTotal, 2, '.', ',')).'';
						$importeSaldoTotal= ''.(string)(number_format($importeSaldoTotal, 2, '.', ',')).'';	
						$RowArray = array(
							's0' =>$i+1,
							's1' =>trim($datarow['razon_social']),
							's2' =>$conteoFactura++,
							's3' =>$importeDocTotal,
							's4' =>$importeCobradoTotal,
							's5' =>	$importeSaldoTotal,			
						);
						$fill = !$fill;					
						$this->total = $this->total -1;			
						$i++;		
						$this-> MultiRow($RowArray,$fill,0);			
									
						$conteoFactura=0;
						$importeDocTotal=0;
						$importeCobradoTotal=0;
						$importeSaldoTotal=0;
			}
			$this->revisarfinPagina($datarow);
			$primero =$datarow['razon_social'];
		}
		$this->cerrarCuadro();		
		$this->cerrarCuadroTotal();			
		$this->tablewidths=$conf_par_tablewidths;
		$this->tablealigns=$conf_par_tablealigns;
		$this->tablenumbers=$conf_par_tablenumbers;
		$this->tableborders=$conf_tableborders;
		$this->tabletextcolor=$conf_tabletextcolor;
	} 
	//desde generarcuerpo
	function revisarfinPagina($a){
		$dimensions = $this->getPageDimensions();
		$hasBorder = false;
		$startY = $this->GetY();
		$this->getNumLines($row['cell1data'], 90);
		$this->calcularMontos($a);			
		if ($startY > 235) {			
			$this->cerrarCuadro();	
		//$this->cerrarCuadroTotal();	//cuanto se usa total	
		if($this->total!= 0){
				$this->AddPage();
				$this->generarCabecera();
			}				
		}
	}
	//
	function Footer() {		
		$this->setY(-15);
		$ormargins = $this->getOriginalMargins();
		$this->SetTextColor(0, 0, 0);
		$line_width = 0.85 / $this->getScaleFactor();
		$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$ancho = round(($this->getPageWidth() - $ormargins['left'] - $ormargins['right']) / 3);
		$this->Ln(2);
		$cur_y = $this->GetY();
		$this->Cell($ancho, 0, '', '', 0, 'L');
		$pagenumtxt = 'Página'.' '.$this->getAliasNumPage().' de '.$this->getAliasNbPages();
		$this->Cell($ancho, 0, $pagenumtxt, '', 0, 'C');
		$this->Cell($ancho, 0, '', '', 0, 'R');
		$this->Ln();
		$fecha_rep = date("d-m-Y H:i:s");
		$this->Cell($ancho, 0, '', '', 0, 'L');
		$this->Ln($line_width);
	}
	//
	//imprimirLinea suma filas
	function calcularMontos($val){
		//var_dump($val['importe_doc']);
		$this->s1 = $this->s1 + $val['importe_doc'];
		$this->s2 = $this->s2 + $val['importe_cobrado_mb'];
		$this->s3 = $this->s3 + $val['saldo_por_cobrar'];					
		$this->t1=$this->t1+$val['importe_doc'];			
		$this->t2=$this->t2+$val['importe_cobrado_mb'];
		$this->t3=$this->t3+$val['saldo_por_cobrar'];
	}	
	//revisarfinPagina pie
	function cerrarCuadro(){		
	
		$conf_par_tablewidths=array(7,60,15,30,30,30,30);			
		$this->tablealigns=array('R','R','R','R','R','R','R');		
		
		$this->tableborders=array('T','T','T','LRTB','LRTB','LRTB','');	
		
		$this->s1= ''.(string)(number_format($this->s1, 2, '.', ',')).'';	
		$this->s2= ''.(string)(number_format($this->s2, 2, '.', ',')).'';
		$this->s3= ''.(string)(number_format($this->s3, 2, '.', ',')).'';						
		$RowArray = array(  's0' => '',
							's1' => '',
							'espacio' => 'Subtotal',
							's2' => $this->s1, 
							's3' => $this->s2,
							's4' => $this->s3,
							
						);		
		$this-> MultiRow($RowArray,false,1);
		$this->s1 = 0;
		$this->s2 = 0;
		$this->s3 = 0;
		$this->s4 = 0;
		$this->s5 = 0;
	}
	//revisarfinPagina pie
	function cerrarCuadroTotal(){
		$conf_par_tablewidths=array(7,60,15,30,30,30,30);					
		$this->tablealigns=array('R','R','R','R','R','R','R');		
		$this->tablenumbers=array(0,0,0,0,2,2,2);
		$this->tableborders=array('','','','LRTB','LRTB','LRTB','');//dibuja las celdas q se habbilitan en el total
		
		//$this->t1= ''.(string)(number_format($this->t1, 2, '.', ',')).'';	
		//$this->t2= ''.(string)(number_format($this->t2, 2, '.', ',')).'';
		//$this->t3= ''.(string)(number_format($this->t3, 2, '.', ',')).'';	
		  
		 
		 /*
		if(($this->t3)<0){
			$this->t3=($this->t3)*-1;
			$this->t3= '('.(string)(number_format($this->t3, 2, '.', ',')).')';
			$this->tablenumbers=array(0,0,0,0,2,2,0);		
		}else{
			$this->tablenumbers=array(0,0,0,0,0,0,2);
		}	*/							
		$RowArray = array(
					't0' => '', 
					't1' => '',
					'espacio' => 'TOTAL: ',
					't2' => $this->t1,
					't3' => $this->t2,
					't4' => $this->t3,
					
				);
		$this-> MultiRow($RowArray,false,1);
	}
	
	function cab() {
		$var = array();
		$desc = array();
		$white = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
		$black = array('T' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$this->Ln(3);
		$this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 10,5,40,20);
		$this->ln(5);
		$this->SetFont('','B',12);		
		$this->Cell(0,5,"Clientes",0,1,'C');					
		$this->Ln(3);		
		/*
		if($this->objParam->getParametro('desde')!=null){
			$desde = $this->objParam->getParametro('desde');
			array_push($var,$desde);
			array_push($desc,'DESDE:');
			$cant++;	
		}*/
		//				
		//$height = 1;
		//$width1 = 5;
		$esp_width = 5;
		$width_c1= 30;
		$width_c2= 50;	
		for($i=0;$i<=$cant;$i++){
			$this->SetFont('', 'B',6);
			$this->SetFillColor(192,192,192, false);
			if($i%2==0){
				$this->Cell($width1, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
				$this->Cell($width_c1, $height, $desc[$i], 0, 0, 'L', false, '', 0, false, 'T', 'C');
				$this->SetFont('', '',6);				
				$this->Cell($width_c2, $height, $var[$i], 0, 0, 'L', true, '', 0, false, 'T', 'C');
			}else{
				$this->Cell($esp_width, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
				$this->Cell(30, $height,$desc[$i], 0, 0, 'L', false, '', 0, false, 'T', 'C');
				$this->SetFont('', '',6);
				$this->Cell(50, $height, $var[$i], 0, 0, 'L', true, '', 0, false, 'T', 'C');
				$this->Ln();
			}			
		}
					
		$this->Ln(4);
		$this->SetFont('','B',6);
		$this->generarCabecera();
	}	
}
?>