<?php
// Extend the TCPDF class to create custom MultiRow
 /*ISSUE				FECHA			AUTHOR		  DESCRIPCION
 * 1B				24/08/2018			EGS				se hizo cambios para cobros regularizados y retencion de garantias 
*/
   
class RCobroReporte extends ReportePDF {
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
	var $s7;
	var $s8;
	
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
	var $cobro;
	var $primero;
	var $tipo;
	function datosHeader ($detalle) {
		//var_dump('reporte 1',$detalle);
		
		$this->SetHeaderMargin(10);
		$this->SetAutoPageBreak(TRUE, 10);
		$this->ancho_hoja = $this->getPageWidth()-PDF_MARGIN_LEFT-PDF_MARGIN_RIGHT-10;
		$this->datos_detalle = $detalle;
		//$this->datos_titulo = $resultado;
		$this->SetMargins(10, 15, 5,10);
	}

	function Header() {		
	}
	//	
	function generarCabecera(){
		$conf_par_tablewidths=array(7,40,15,25,25,15,25,20,25);
		$conf_par_tablenumbers=array(0,0,0,0,0,0,0,0,0);
		$conf_par_tablealigns=array('C','C','C','C','C','C','C','C','C');
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
							's1' => 'Proveedor',
							's2' => 'Fecha Cobro:',
							's3' => 'Nro Tramite:',
							's4' => 'Importe Cobro:',
							's5' => 'Moneda:',
							's6' => 'Importe Cobro (BS):',
							's7' => 'Nro Facturas',
							's8' => 'Cobro a Factura(BS):'
							
		
						);
		$this->MultiRow($RowArray, false, 1);
	}
	//
	function generarReporte($detalle,$tipo,$proveedor) {
		$this->cobro = $detalle->getParameter('cobro');
		$this->setFontSubsetting(false);
		$this->AddPage();
		$this->generarCuerpo($this->cobro,$tipo,$proveedor);
	}
	//		
	function generarCuerpo($detalle,$tipo,$proveedor){		
		//function
		$this->cabezera($detalle,$tipo,$proveedor);
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
		$this->s6 = 0;
		$this->s7 = 0;
		$this->s8 = 0;
		$this->imprimirLinea($val,$count,$fill);
	}
	//desde 
	function imprimirLinea($val,$count,$fill){
		$this->SetFillColor(224, 235, 255);
		$this->SetTextColor(0);
		$this->SetFont('','',6);

		$primero=0;

		
		//var_dump($this->factura);
		$primero= 0;
		foreach ($this->cobro as $datarow) {
			//var_dump('foreach',$datarow); 
				//var_dump('foreach',$datarow['id_cobro_simple']);
					if($primero != $datarow['id_cobro_simple']) {		
						$this->tablealigns=array('C','L','L','L','R','R','R','R','R','R');	// las alings de las tablas center left right	
							
						$this->tableborders=array('RLT','RLT','RLT','RLT','RLT','RLT','RLT','RLT','RLT','RLT'); // los bordes de la tabla right left top botton
						$this->tabletextcolor=array();	
						
				
						$RowArray = array(
							's0' =>$i+1,
							's1' =>trim($datarow['desc_proveedor']),
							's2' =>trim($datarow['fecha']),
							's3' =>trim($datarow['nro_tramite']),
							's4' =>trim(number_format($datarow['importe'], 2, '.', ',')),
							's5' =>trim($datarow['desc_moneda']),
							's6' =>trim(number_format($datarow['importe_mb'], 2, '.', ',')),
							's7' =>trim($datarow['nro_documento']),
							's8'=>trim(number_format($datarow['importe_cobro_factura'], 2, '.', ',')),
								
						);
						$fill = !$fill;					
						$this->total = $this->total -1;			
						$i++;		
						$this-> MultiRow($RowArray,$fill,0);
						
						
				}else{
						$this->tablealigns=array('C','R','L','R','R','R','R','R','R','R');	// las alings de las tablas center left right		
						$this->tableborders=array('RL','RL','RL','RL','RL','RL','RL','RL','RL'); // los bordes de la tabla right left top botton
						$this->tabletextcolor=array();	
			
						$RowArray = array(
							's0' =>'',
							's1' =>'',
							's2' =>'',
							's3' =>'',
							's4' =>'',
							's5' =>'',
							's6' =>'',
							's7' =>$datarow['nro_documento'],
							's8'=>number_format($datarow['importe_cobro_factura'], 2, '.', ','),
								
						);
						$fill = !$fill;					
						$this->total = $this->total -1;			
								
						$this-> MultiRow($RowArray,$fill,0);			
				
				}
			
			$this->revisarfinPagina($datarow);
			$primero =$datarow['id_cobro_simple'];
		}
		//$this->cerrarCuadro();		
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
	
		if($this->primero != $val['id_cobro_simple']) {
		
		$this->s1 = $this->s1 + $val['importe_mb'];
		$this->s2 = $this->s2 + $val['importe_cobro_factura'];
							
		$this->t1=$this->t1+$val['importe_mb'];			
		
		

		}
		$this->t2=$this->t2+$val['importe_cobro_factura'];
		$this->primero = $val['id_cobro_simple'];
	}	
	//revisarfinPagina pie
	function cerrarCuadro(){		
		
		
		$conf_par_tablewidths=array(7,40,15,30,25,15,15,20,20);				
		$this->tablealigns=array('R','R','L','R','R','R','R','R','R');	
		//$this->tablenumbers=array(0,0,0,2,0,0,2,2);
		$this->tableborders=array('T','T','T','T','T','T','LRTB','T','LRTB');	
		
		$this->s1= ''.(string)(number_format($this->s1, 2, '.', ',')).'';	
		$this->s2= ''.(string)(number_format($this->s2, 2, '.', ',')).'';
		$this->s3= ''.(string)(number_format($this->s3, 2, '.', ',')).'';						
		$RowArray = array(  
							's0' => '',
							's1' => '', 
							's2' => '',
							's3' => '',
							's4' => '',
							'espacio' => 'Subtotal: ',
							's5' => $this->s1,
							'espacio1' => 'Subtotales Cobrado: ',
							's6' => $this->s2,
							
							
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
		$conf_par_tablewidths=array(7,40,15,30,25,15,15,20,20);
					
		$this->tablealigns=array('R','R','L','R','R','R','R','R','R');	
		$this->tablenumbers=array(0,0,0,0,0,0,2,0,2);
		$this->tableborders=array('T','T','T','T','T','T','LRTB','T','LRTB');//dibuja las celdas q se habbilitan en el total
		
		//$this->t1= ''.(string)(number_format($this->t1, 2, '.', ',')).'';	
		//$this->t2= ''.(string)(number_format($this->t2, 2, '.', ',')).'';
		$this->t3= (number_format($this->t3, 2, '.', ','));	
		  
		 
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
					't2' => '',
					't3' => '',
					't4' => '',
					'espacio' => 'Total : ',
					't5' => $this->t1,
					'espacio1' =>'Total Cobrado: ',
					't6' => $this->t2,
					
				);
		$this-> MultiRow($RowArray,false,1);
	}
	
	function cabezera($detalle,$tipo,$proveedor) {
		$tipo_nombre= null;
		$datos=$detalle;
		//var_dump($proveedor);
		if($this->objParam->getParametro('id_tipo_cobro_simple')!= ''){
			$datos_tipo=$tipo->getParameter('tipo_cobro');
			$tipo_nombre = strtoupper($datos_tipo[0][nombre]);
			
		}
		
		
		//var_dump('Tipo',$tipo_nombre);
		$var = array();
		$desc = array();
		$white = array('LTRB' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(255, 255, 255)));
		$black = array('T' =>array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
		$this->Ln(3);
		$this->Image(dirname(__FILE__).'/../../lib/imagenes/logos/logo.jpg', 10,5,40,20);
		$this->ln(5);
		$this->SetFont('','B',12);		
		$this->Cell(0,5,"Cobros",0,1,'C');					
		$this->Ln(5);	
		
		//Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=0, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
	
		//coloca los datos del cliente en la cabecera 
		
		if($this->objParam->getParametro('tipo')=='proveedor'){
				
			$datos_Proveedor =$proveedor->getParameter('proveedor');
			
		
			$nombre_proveedor = $datos_Proveedor[0][desc_proveedor];
				//var_dump($nombre_proveedor);
			
		}

		if($this->objParam->getParametro('tipo')=='todo'){
			$tipo = strtoupper($this->objParam->getParametro('tipo'));
			array_push($var,$tipo);
			array_push($desc,$tipo_nombre);
			/*
			array_push($var,$nit);
			array_push($desc,'Razon Social:');
			array_push($desc,'Nit:');*/
			$cant++;	
		}
		else{
			$tipo = strtoupper($this->objParam->getParametro('tipo'));
			array_push($var,$tipo);
			array_push($desc,$tipo_nombre);
			
			
		}
		
		//				
		$height = 1;
		$width1 = 5;
		$esp_width = 5;
		$width_c1= 200;
		$width_c2= 200;	
		for($i=0;$i<=$cant;$i++){
			$this->SetFont('', 'B',9);
			$this->SetFillColor(255,255,255, false); ///color blanco al campo de todo 
			if($i%2==0){
					
				$this->Cell($width_c2, $height, $var[$i], 0, 0, 'C', true, '', 0, false, 'T', 'C');
				$this->Ln();
				
				if($this->objParam->getParametro('tipo')=='proveedor'){
					
				$this->Cell($width_c1, $height, $nombre_proveedor, 0, 0, 'C', false, '', 0, false, 'T', 'C');
				$this->Ln(4);	
			
				}
				
				$this->Cell($width_c1, $height, $desc[$i], 0, 0, 'C', false, '', 0, false, 'T', 'C');
				$this->Ln(4);
							
				
				
				
			}else{
				$this->Cell($esp_width, $height, '', 0, 0, 'L', false, '', 0, false, 'T', 'C');
				$this->Cell($width_c1, $height,$desc[$i], 0, 0, 'L', false, '', 0, false, 'T', 'C');
				
				$this->Ln(3);
			}			
		}
					
		$this->Ln(4);
		$this->SetFont('','B',6);
		$this->generarCabecera();
	}	
}
?>