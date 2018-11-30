<?php
 
class RCobroReporteXls
{
	private $docexcel;
	private $objWriter;
	private $numero;
	private $equivalencias=array();
	private $objParam;
	public  $url_archivo;
	var $liquido;
	var $descuento;
	var $importe;
	var $fila1;
	function __construct(CTParametro $objParam)
	{
		//var_dump($objParam);
		$this->objParam = $objParam;
		$this->url_archivo = "../../../reportes_generados/".$this->objParam->getParametro('nombre_archivo');
		set_time_limit(400);
		$cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array('memoryCacheSize'  => '10MB');
		PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);
		
		$this->docexcel = new PHPExcel();
		$this->docexcel->getProperties()->setCreator("PXP")
			->setLastModifiedBy("PXP")
			->setTitle($this->objParam->getParametro('titulo_archivo'))
			->setSubject($this->objParam->getParametro('titulo_archivo'))
			->setDescription('Reporte "'.$this->objParam->getParametro('titulo_archivo').'", generado por el framework PXP')
			->setKeywords("office 2007 openxml php")
			->setCategory("Report File");
		$this->equivalencias=array( 0=>'A',1=>'B',2=>'C',3=>'D',4=>'E',5=>'F',6=>'G',7=>'H',8=>'I',
		9=>'J',10=>'K',11=>'L',12=>'M',13=>'N',14=>'O',15=>'P',16=>'Q',17=>'R',
		18=>'S',19=>'T',20=>'U',21=>'V',22=>'W',23=>'X',24=>'Y',25=>'Z',
		26=>'AA',27=>'AB',28=>'AC',29=>'AD',30=>'AE',31=>'AF',32=>'AG',33=>'AH',
		34=>'AI',35=>'AJ',36=>'AK',37=>'AL',38=>'AM',39=>'AN',40=>'AO',41=>'AP',
		42=>'AQ',43=>'AR',44=>'AS',45=>'AT',46=>'AU',47=>'AV',48=>'AW',49=>'AX',
		50=>'AY',51=>'AZ',
		52=>'BA',53=>'BB',54=>'BC',55=>'BD',56=>'BE',57=>'BF',58=>'BG',59=>'BH',
		60=>'BI',61=>'BJ',62=>'BK',63=>'BL',64=>'BM',65=>'BN',66=>'BO',67=>'BP',
		68=>'BQ',69=>'BR',70=>'BS',71=>'BT',72=>'BU',73=>'BV',74=>'BW',75=>'BX',
		76=>'BY',77=>'BZ');

	}
	function imprimeCabecera($shit,$tipo) {
	
  //var_dump($shit);
        $this->docexcel->createSheet($shit);
        $this->docexcel->setActiveSheetIndex($shit);
        $this->docexcel->getActiveSheet()->setTitle($tipo);
	
		$styleTitulos2 = array(
			'font'  => array(
				'bold'  => true,
				'size'  => 9,
				'name'  => 'Arial',
				'color' => array(
					'rgb' => 'FFFFFF'
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
					'rgb' => '2D83C5'
				)
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$styleTitulos3 = array(
			'font'  => array(
				'bold'  => true,
				'size'  => 12,
				'name'  => 'Arial',
				'color' => array(
					'rgb' => 'FFFFFF'
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
					'rgb' => '707A82'
				)
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
	$styleTitulos4 = array(
			'font'  => array(
				'bold'  => true,
				'size'  => 9,
				'name'  => 'Arial',
				'color' => array(
					'rgb' => 'FFFFFF'
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
					'rgb' => 'DB9E91'
				)
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		
		
		//
		$datos = $this->objParam->getParametro('data');
		
		//var_dump($this->objParam->getParametro('tipo'));
		$tipo = strtoupper ($this->objParam->getParametro('tipo'));
		
		//var_dump($tipo_nombre);
		
		if($this->objParam->getParametro('id_tipo_cobro_simple')!=''){
					
					
					 $dataTipo =$this->objParam->getParametro('tipo_cobro');
					 $tipo_nombre= $dataTipo[0][nombre]	;
					
		}
		if($this->objParam->getParametro('id_proveedor')!=''){
					 	
					
					
					 $dataProveedor =$this->objParam->getParametro('proveedor');
					 //var_dump($dataProveedor);
					 $nombre_proveedor= $dataProveedor[0][desc_proveedor]	;
					 
					
		}
	
		
		if($shit==1){
					
					if($this->objParam->getParametro('id_tipo_cobro_simple')!=''){
	
						$this->docexcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleTitulos3);
					
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,1,$tipo_nombre);

					}
					else{
						$this->docexcel->getActiveSheet()->getStyle('A1:I1')->applyFromArray($styleTitulos4);				
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,1,'COBROS');
						
					}
	
					
					if($this->objParam->getParametro('id_proveedor')!=''){
	
						
						$this->docexcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($styleTitulos4);				
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,$nombre_proveedor);

					}
					else{
					$this->docexcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray($styleTitulos4);				
					$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0,2,$tipo);
					}
											
					//aplica estilo a una linea a una fila de celdas
				
					
					
					$this->docexcel->getActiveSheet()->getStyle('A3:I3')->applyFromArray($styleTitulos2);
				
					//SE COLOCA LAS DIMENSIONES QUE TENDRA LAS CELDAS
					$this->docexcel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
					$this->docexcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
					$this->docexcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
					$this->docexcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
					$this->docexcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);
					$this->docexcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);
					$this->docexcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);
					$this->docexcel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
					$this->docexcel->getActiveSheet()->getColumnDimension('J')->setWidth(18);
					$this->docexcel->getActiveSheet()->getColumnDimension('K')->setWidth(18);
					$this->docexcel->getActiveSheet()->getColumnDimension('L')->setWidth(18);
					$this->docexcel->getActiveSheet()->getColumnDimension('M')->setWidth(18);
					
					$this->docexcel->getActiveSheet()->getColumnDimension('N')->setWidth(18);
					$this->docexcel->getActiveSheet()->getColumnDimension('O')->setWidth(18);
					$this->docexcel->getActiveSheet()->getColumnDimension('P')->setWidth(18);
					$this->docexcel->getActiveSheet()->getColumnDimension('Q')->setWidth(18);
					$this->docexcel->getActiveSheet()->getColumnDimension('R')->setWidth(18);
					$this->docexcel->getActiveSheet()->getColumnDimension('S')->setWidth(20);
					$this->docexcel->getActiveSheet()->getColumnDimension('T')->setWidth(18);	
					$this->docexcel->getActiveSheet()->getColumnDimension('U')->setWidth(25);
					$this->docexcel->getActiveSheet()->getColumnDimension('V')->setWidth(25);						
					//*************************************Cabecera************************//
					//automaticamente selecciona el campo en las celdas
					//$this->docexcel->getActiveSheet()->getStyle('A3:G3')->getAlignment()->setWrapText(true);
					
					
					 //une celdas 
				
					 
					 $this->docexcel->getActiveSheet()->mergeCells('A1:I1');
					 $this->docexcel->getActiveSheet()->mergeCells('A2:I2');
					
	
					$this->docexcel->getActiveSheet()->setCellValue('A3','Nº');
					$this->docexcel->getActiveSheet()->setCellValue('B3','PROVEEDOR');
					$this->docexcel->getActiveSheet()->setCellValue('C3','FECHA COBRO');
					$this->docexcel->getActiveSheet()->setCellValue('D3','Nº TRAMITE');
					$this->docexcel->getActiveSheet()->setCellValue('E3','IMPORTE COBRO');
					$this->docexcel->getActiveSheet()->setCellValue('F3','MONEDA');	
					$this->docexcel->getActiveSheet()->setCellValue('G3','IMPORTE COBRO (BS)');
					$this->docexcel->getActiveSheet()->setCellValue('H3','NRO DOC/FACT');
					$this->docexcel->getActiveSheet()->setCellValue('I3','COBRO A FACTURA (BS)');		
				
					
				
				
		}		
	}

	function generarDatos()
	{
		$styleTitulos3 = array(
			'font'  => array(
				'bold'  => true,
				'size'  => 10,
				'name'  => 'Arial',
				'color' => array(
					'rgb' => 'FFFFFF'
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
					'rgb' => '2D83C5'
				)
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		$styleTitulos5 = array(
			'font'  => array(
				'bold'  => false,
				'size'  => 9,
				'name'  => 'Arial',
				'color' => array(
					'rgb' => '000000'
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
					'rgb' => 'FFFFFF'
				)
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
			$styleTitulos6 = array(
			'font'  => array(
				'bold'  => false,
				'size'  => 9,
				'name'  => 'Arial',
				'color' => array(
					'rgb' => '000000'
				)
			),
			'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
				'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			),
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array(
					'rgb' => 'FFFFFF'
				)
			),
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);
		
		
		$this->numero = 1;
		$fila = 4;
		
		$datos = $this->objParam->getParametro('data');
		
		
		$this->imprimeCabecera(1,'Cobros');
		//var_dump(empty($datos));
		
		$primero=0;
		
		if ( !empty($datos)) {

		foreach ($datos as $value){
				
						
					if($primero != $value['id_cobro_simple'] ){
							
						$cobroFactura=$value['importe_cobro_factura'];
						$saldo = $saldo - $cobroFactura ;
				
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(0, $fila, $this->numero);
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(1, $fila, trim($value['desc_proveedor']));
				     	$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(2, $fila, $value['fecha']);
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(3, $fila, $value['nro_tramite']);
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(4, $fila, $value['importe']);
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5, $fila, $value['desc_moneda']);
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6, $fila, $value['importe_mb']);
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['nro_documento']);
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['importe_cobro_factura']);
						
						
						$this->numero++;
					}else{
						$this->docexcel->getActiveSheet()->mergeCells('A'.($fila-1).':A'.$fila.'');
						$this->docexcel->getActiveSheet()->mergeCells('B'.($fila-1).':B'.$fila.'');
						$this->docexcel->getActiveSheet()->mergeCells('C'.($fila-1).':C'.$fila.'');
						$this->docexcel->getActiveSheet()->mergeCells('D'.($fila-1).':D'.$fila.'');
						$this->docexcel->getActiveSheet()->mergeCells('E'.($fila-1).':E'.$fila.'');
						$this->docexcel->getActiveSheet()->mergeCells('F'.($fila-1).':F'.$fila.'');
						$this->docexcel->getActiveSheet()->mergeCells('G'.($fila-1).':G'.$fila.'');
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(7, $fila, $value['nro_documento']);
						$this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila, $value['importe_cobro_factura']);
						
					}
					
			$primero =$value['id_cobro_simple'];
			$fila++;
							
			}

		
		
		   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(5,$fila,'TOTALES:');
           $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(6,$fila,'=SUM(G4:G'.($fila-1).')');
		   $this->docexcel->getActiveSheet()->setCellValueByColumnAndRow(8, $fila,'=SUM(I4:I'.($fila-1).')');
		   
		   $this->docexcel->getActiveSheet()->getStyle('A'.(4).':A'.($fila-1).'')->applyFromArray($styleTitulos5);
		   $this->docexcel->getActiveSheet()->getStyle('B'.(4).':B'.($fila-1).'')->applyFromArray($styleTitulos6);
		   $this->docexcel->getActiveSheet()->getStyle('C'.(4).':C'.($fila-1).'')->applyFromArray($styleTitulos5);
		   $this->docexcel->getActiveSheet()->getStyle('D'.(4).':D'.($fila-1).'')->applyFromArray($styleTitulos5);///EGS//
		   $this->docexcel->getActiveSheet()->getStyle('E'.(4).':E'.($fila-1).'')->applyFromArray($styleTitulos5);
		   $this->docexcel->getActiveSheet()->getStyle('F'.(4).':F'.($fila-1).'')->applyFromArray($styleTitulos5);
		   $this->docexcel->getActiveSheet()->getStyle('G'.(4).':G'.($fila).'')->applyFromArray($styleTitulos5);
		   $this->docexcel->getActiveSheet()->getStyle('H'.(4).':H'.($fila-1).'')->applyFromArray($styleTitulos5);
		   $this->docexcel->getActiveSheet()->getStyle('I'.(4).':I'.($fila).'')->applyFromArray($styleTitulos5);
		    $this->docexcel->getActiveSheet()->getStyle('I'.($fila).':I'.($fila).'')->applyFromArray($styleTitulos5);
		   
		   
		   $this->docexcel->getActiveSheet()->getStyle('E'.(4).':E'.($fila).'')->getNumberFormat()->setFormatCode('#,##0.00');///EGS//
		   $this->docexcel->getActiveSheet()->getStyle('G'.(4).':G'.($fila).'')->getNumberFormat()->setFormatCode('#,##0.00');
		   $this->docexcel->getActiveSheet()->getStyle('I'.(4).':I'.($fila).'')->getNumberFormat()->setFormatCode('#,##0.00');	
		   //$this->docexcel->getActiveSheet()->getStyle('H'.($fila).':I'.($fila).'')->getNumberFormat()->setFormatCode('#,##0.00');
		   
		 	$this->docexcel->getActiveSheet()->getStyle('I'.($fila).':I'.($fila).'')->getAlignment()->setWrapText(true);  

		}
	}
	
	function generarReporte(){
		$this->objWriter = PHPExcel_IOFactory::createWriter($this->docexcel, 'Excel5');
		$this->objWriter->save($this->url_archivo);
	}
	
	
	function generarResultado ($sheet,$a){
		$this->docexcel->createSheet($sheet);
		$this->docexcel->setActiveSheetIndex(0);
		$this->imprimeCabecera($sheet,'TOTAL');
		$this->docexcel->getActiveSheet()->setTitle('TOTALES');
		$this->docexcel->getActiveSheet()->setCellValue('E5','TOTAL');
		$this->docexcel->getActiveSheet()->setCellValue('F5',$a);
		
	}
	
}
?>
