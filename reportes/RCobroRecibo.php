<?php
// Extend the TCPDF class to create custom MultiRow
class RCobroRecibo extends  ReportePDF {
	var $cabecera;
	var $detalleCbte;
	var $ancho_hoja;
	var $gerencia;
	var $numeracion;
	var $ancho_sin_totales;
	var $cantidad_columnas_estaticas;
	var $total;
	var $with_col;
	var $tot_debe;
	var $tot_haber;
	var $tot_debe_mb;
	var $tot_haber_mb;
	
	function datosHeader ( $detalle) {
		//var_dump($detalle);
		
		
		 	$this->cabecera = $detalle->getParameter('cabecera');
	        $this->detalleCbte = $detalle->getParameter('detalleCbte');			
			$this->ancho_hoja = $this->getPageWidth() - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT-10;
			$this->datos_detalle = $detalle;
		 	$this->SetMargins(15, 30, 5);
		 
		 
	}
	
	function Header() {
		
		 	   // $this->SetMargins(15, 40, 5);
				$newDate = date("d/m/Y", strtotime( $this->cabecera[0]['fecha']));		
				$dataSource = $this->datos_detalle; 
			    ob_start();
				include(dirname(__FILE__).'/../reportes/tpl/recibo/cabecera.php');
		        $content = ob_get_clean();
				$this->writeHTML($content, true, false, true, false, '');
		
	}
	
	
   
  
   
  
	
	 function generarReporte() {
	 	
		$this->AddPage();
		
		$dataSource = $this->datos_detalle; 
		$tot_debe = 0;
		$tot_haber = 0;
		if ($this->cabecera[0]['id_moneda'] == $this->cabecera[0]['id_moneda_base']){
		    $this->with_col = '55%';
	    }
	    else{
		   $this->with_col = '45%';
	    }
		
		$with_col = $this->with_col;
		
		
		//adiciona glosa
		ob_start();
		include(dirname(__FILE__).'/../reportes/tpl/recibo/glosa.php');
        $content = ob_get_clean();
		
		ob_start();
		include(dirname(__FILE__).'/../reportes/tpl/recibo/cabeceraDetalle.php');
        $content2 = ob_get_clean();
		$this->writeHTML($content.$content2, false, false, true, false, '');
		
		$this->SetFont ('helvetica', '', 5 , '', 'default', true );
		
		//$this->Ln(2);
	
		//ob_start();
		// $content = ob_get_clean();
		//$this->writeHTML($content, true, false, true, false, '');
		
		//$this->Ln();
		$this->revisarfinPagina($content);
	    $this->subtotales('TOTALES');	
		
		$this->Ln(2);
		$this->Firmas();
		
		$this->Cell(185,3.5,'Reg: '.$this->cabecera[0]['usr_reg'],'',0,'R');
		$this->Cell(10,3.5,'ID: '.$this->cabecera[0]['id_int_comprobante'],'',0,'R');
		
		
		
		  	
		
	} 

   function Firmas() {
			
		
		$newDate = date("d/m/Y", strtotime( $this->cabecera[0]['fecha']));		
		$dataSource = $this->datos_detalle; 
	    ob_start();
		include(dirname(__FILE__).'/../reportes/tpl/recibo/firmas.php');
        $content = ob_get_clean();
		$this->writeHTML($content, true, false, true, false, '');
		
		
		
	}

   function subtotales ($titulo){
   	  ob_start();
	  include(dirname(__FILE__).'/../reportes/tpl/recibo/totales.php');
      $content = ob_get_clean();
	  $this->writeHTML($content, false, false, true, false, '');
	
   }
	
	
   function revisarfinPagina($content){
		$dimensions = $this->getPageDimensions();
		$hasBorder = false; //flag for fringe case
		
		$startY = $this->GetY();
		$test = $this->getNumLines($content, 80);
		
		//if (($startY + 10 * 6) + $dimensions['bm'] > ($dimensions['hk'])) {
		    
		//if ($startY +  $test > 250) {
		$auxiliar = 250;
		//if($this->page==1){
		//	$auxiliar = 250;	
		//}
		if ($startY +  $test > $auxiliar) {
			//$this->Ln();
			//$this->subtotales('Pasa a la siguiente página. '.$startY);
			$this->subtotales('Pasa a la siguiente página');
			$startY = $this->GetY();			
			if($startY < 70){
				//$this->AddPage();
			}
			else{
				$this->AddPage();
			}
			
			
			//$this->writeHTML('<p>text'.$startY.'</p>', false, false, true, false, '');		    
		} 
	}
}
?>